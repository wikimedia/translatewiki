# Activity Audit

Upstream activity compliance audit for translatewiki.net projects per
[T383652](https://phabricator.wikimedia.org/T383652).

## Criteria

- **Integration**: upstream commit within last 3 months (≤92 days)
- **Availability**: upstream commit within last 12 months (≤365 days)

Commits by `l10n-bot@translatewiki.net`, `translatewiki.net`, or
`Translation updater bot` are excluded — only upstream activity counts.

## Running the audit

```bash
source ~/venv/bin/activate
php ~/audit/parse-repos.php > /tmp/repos.json
python3 ~/audit/audit-server.py /tmp/repos.json > ~/audit/activity-audit-2026.wiki
```

### What the scripts do

1. **parse-repos.php** — Reads `/home/betawiki/config/repoconfig.yaml` and the generated extension/skin lists from
   `/home/betawiki/config/groups/MediaWiki/`. Outputs a flat JSON of all repos with project name, repo key, URL, and
   type.

2. **audit-server.py** — For each repo in the JSON, runs
   `git log` on the local checkout at `/resources/projects/<repo_key>` to find the most recent non-bot commit. Outputs a
   complete wiki-formatted file with a sortable table and summary section.

### Notes

- The Python script temporarily sets `git config --global safe.directory '*'`
  to access repos owned by `l10n-bot`, and removes it when done.
- No writes are made to any repository. All operations are read-only.
- No external network calls. Everything uses local git history.
- Python 3.6+ stdlib only, no pip dependencies.

## Output

The wiki file contains:

- A sortable table with columns: Project, Repo, Upstream URL, Last Commit, Days Since, Status
- A summary with totals per status
- Subsections listing ❌ removals and ⚠️ follow-ups

The latest audit is at https://translatewiki.net/wiki/Translating:Activity_Audit_2026

## TODO

- [ ] Correct Gerrit web URLs in output — currently links to gitiles browse URLs, should probably be Phabricator URLs
  where applicable.
- [ ] Handle `nocc` (SVN/SourceForge) — currently shows as MISSING since there's no local git checkout.
