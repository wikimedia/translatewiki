#!/usr/bin/env python3
"""
Activity audit script - run on the translatewiki.net server.

Usage:
    source ~/venv/bin/activate
    php /resources/projects/audit/parse-repos.php > /tmp/repos.json
    python3 /resources/projects/audit/audit-server.py /tmp/repos.json > ~/audit/activity-audit-2026.wiki

This script:
1. Reads the repo list from JSON (output of parse-repos.php)
2. For each repo, runs git log locally to find last non-bot commit
3. Outputs a complete wiki-formatted audit file
"""

import json
import subprocess
import sys
from datetime import datetime, timezone
from pathlib import Path

TODAY = datetime.now(timezone.utc)
THREE_MONTHS = 92
TWELVE_MONTHS = 365

REPOS_BASE = '/resources/projects'

# Patterns to skip in git log --author (perl-regexp)
BOT_PATTERN = r'^(?!.*(l10n-bot@translatewiki\.net|translatewiki\.net|Translation updater bot)).*$'


def get_last_non_bot_commit(repo_dir):
    """Use git log to find the last non-bot commit date in a local checkout."""
    full_path = f'{REPOS_BASE}/{repo_dir}'
    if not Path(full_path).joinpath('.git').exists():
        return None, 'MISSING'

    try:
        result = subprocess.run(
            [
                'git', '-C', full_path, 'log', '--all',
                '--perl-regexp',
                f'--author={BOT_PATTERN}',
                '--format=%aI',  # ISO 8601 author date
                '-n', '1',
            ],
            capture_output=True, text=True, timeout=30
        )

        if result.returncode != 0 or not result.stdout.strip():
            # Try without --all (maybe the repo has only one branch)
            result = subprocess.run(
                [
                    'git', '-C', full_path, 'log',
                    '--perl-regexp',
                    f'--author={BOT_PATTERN}',
                    '--format=%aI',
                    '-n', '1',
                ],
                capture_output=True, text=True, timeout=30
            )

        output = result.stdout.strip()
        if not output:
            return None, 'BOT_ONLY'

        # Parse ISO date
        dt = datetime.fromisoformat(output)
        return dt, None

    except subprocess.TimeoutExpired:
        return None, 'TIMEOUT'
    except Exception as e:
        return None, f'ERROR: {e}'


def compute_status(dt):
    """Compute days since and status."""
    if dt is None:
        return None, None, None

    days = (TODAY - dt).days
    date_str = dt.strftime('%Y-%m-%d')

    if days <= THREE_MONTHS:
        status = '✅ PASS'
    elif days <= TWELVE_MONTHS:
        status = '⚠️ INTEGRATION'
    else:
        status = '❌ AVAILABILITY'

    return date_str, days, status


def main():
    if len(sys.argv) < 2:
        print("Usage: python3 audit-server.py <repos.json>", file=sys.stderr)
        print("Generate with: php audit/parse-repos.php > repos.json", file=sys.stderr)
        sys.exit(1)

    # Allow git to access repos owned by other users
    subprocess.run(
        ['git', 'config', '--global', '--add', 'safe.directory', '*'],
        check=True
    )

    repos_file = sys.argv[1]
    with open(repos_file) as f:
        data = json.load(f)

    # Build flat list
    rows = []
    for project in sorted(data.keys()):
        info = data[project]
        repos = info.get('repos', {})
        for repo_key in sorted(repos.keys()):
            repo = repos[repo_key]
            url = repo.get('url', '')
            if not url:
                continue
            # Clean URL for display
            display_url = url.rstrip('/')
            if display_url.endswith('.git'):
                display_url = display_url[:-4]
            rows.append((project, repo_key, display_url))

    # Print header
    print("= translatewiki.net Project Activity Audit 2026 =")
    print()
    print(f"; Date: {TODAY.strftime('%Y-%m-%d')}")
    print("; Task: [[phab:T383652]]")
    print("; Criteria")
    print(":; Integration: commit within last 3 months (≤92 days)")
    print(":; Availability: commit within last 12 months (≤365 days)")
    print("; Status legend")
    print(":; ✅ PASS: Active within 3 months")
    print(":; ⚠️ INTEGRATION: Last commit 3–12 months ago")
    print(":; ❌ AVAILABILITY: Last commit over 12 months ago")
    print()
    print('{| class="wikitable sortable"')
    print("|-")
    print("! Project !! Repo !! Upstream URL !! Last Commit !! Days Since !! Status")

    # Process
    stats = {'pass': 0, 'integration': 0, 'availability': 0, 'error': 0}
    failures = []
    warnings = []

    total = len(rows)
    for i, (project, repo_key, display_url) in enumerate(rows, 1):
        print(f"[{i}/{total}] {project}/{repo_key}", file=sys.stderr)

        dt, error = get_last_non_bot_commit(repo_key)

        if error:
            print(f"  {error}", file=sys.stderr)
            date_str = error
            days_str = '-'
            status_str = '❓ ERROR'
            stats['error'] += 1
        else:
            date_str, days, status_str = compute_status(dt)
            days_str = str(days)
            if '❌' in status_str:
                stats['availability'] += 1
                failures.append((project, repo_key, display_url, date_str, days))
            elif '⚠️' in status_str:
                stats['integration'] += 1
                warnings.append((project, repo_key, display_url, date_str, days))
            else:
                stats['pass'] += 1

        print(f"|-\n| {project} || {repo_key} || [{display_url}] || {date_str} || {days_str} || {status_str}")

    print("|}")
    print()

    # Summary
    print("== Summary ==")
    print()
    checked = stats['pass'] + stats['integration'] + stats['availability'] + stats['error']
    print(f"; Total checked: {checked}")
    print(f"; ✅ PASS: {stats['pass']}")
    print(f"; ⚠️ INTEGRATION: {stats['integration']}")
    print(f"; ❌ AVAILABILITY: {stats['availability']}")
    print(f"; ❓ ERROR: {stats['error']}")
    print()

    if failures:
        print("=== ❌ Removals (last commit >12 months) ===")
        print()
        for project, repo_key, url, date_str, days in sorted(failures):
            print(f"* '''{project}''' / {repo_key} — [{url}] — last commit: {date_str} ({days} days)")
        print()

    if warnings:
        print("=== ⚠️ Follow-ups (last commit 3–12 months) ===")
        print()
        for project, repo_key, url, date_str, days in sorted(warnings):
            print(f"* '''{project}''' / {repo_key} — [{url}] — last commit: {date_str} ({days} days)")
        print()

    print(f"\nDone: {checked} repos checked.", file=sys.stderr)

    # Remove the safe.directory wildcard we added
    subprocess.run(
        ['git', 'config', '--global', '--unset-all', 'safe.directory'],
        check=False  # Don't fail if already removed
    )


if __name__ == '__main__':
    main()
