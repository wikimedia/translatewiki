# = Class: mailer
#
# Provides mail server configuration.
#
class mailer (
  String $domain,
) {
  file { '/etc/mailname':
    content => "${domain}\n"
  }

  package { 'postsrsd':
    ensure => present,
  }

  class { 'postfix':
    manage_aliases  => false,
    manage_mailname => false,
    smtp_listen     => 'all',
    myorigin        => $domain,
  }

  postfix::config {
    'mydomain':                    value => $domain;
    'mydestination':               value => "${domain}, localhost";
    'relay_domains':               value => $domain;
    'virtual_alias_maps':          value => 'regexp:/etc/postfix/virtual_regexp';
    'header_size_limit':           value => '4096000';
    'smtpd_helo_required':         value => 'yes';
    'smtpd_helo_restrictions':     value => @(HEREDOC/L)
       permit_inet_interfaces permit_sasl_authenticated \
       reject_invalid_helo_hostname reject_non_fqdn_helo_hostname \
       reject_unknown_helo_hostname
       |- HEREDOC
    ;
    'smtpd_sender_restrictions':   value => @(HEREDOC/L)
       permit_inet_interfaces permit_sasl_authenticated \
       reject_unknown_reverse_client_hostname reject_unknown_client_hostname \
       reject_unknown_sender_domain
       |- HEREDOC
    ;
    'smtpd_relay_restrictions':    value => 'permit_mynetworks permit_sasl_authenticated defer_unauth_destination';
    'sender_canonical_maps':       value => 'tcp:localhost:10001';
    'sender_canonical_classes':    value => 'envelope_sender';
    'recipient_canonical_maps':    value => 'tcp:localhost:10002';
    'recipient_canonical_classes': value => 'envelope_recipient,header_recipient';
    'smtpd_milters':               value => 'inet:localhost:8891';
    'non_smtpd_milters':           value => '$smtpd_milters';
  }

  postfix::hash { '/etc/postfix/sasl_passwd':
    ensure => 'present',
    source => '/root/postfix/sasl_passwd',
  }

  include ::opendkim;

  # MediaWiki Bounce handler
  package { 'postfix-pcre':
    ensure => present,
  }

  postfix::hash { '/etc/postfix/virtual_regexp':
    ensure  => present,
    content => '/wiki-[a-z0-9_.]+-[a-z0-9]+-[a-z0-9]+-[a-z0-9+\/=]+@translatewiki.net/ bouncehandler',
  }

  # set up the default parameters for all firewall rules
  Firewall {
    before  => Class['base::firewall::post'],
    require => Class['base::firewall::pre'],
  }

  firewall { '030 Allow inbound SMTP (v4)':
    dport    => 25,
    proto    => tcp,
    jump     => accept,
    protocol => 'IPv4',
  }

  firewall { '030 Allow inbound SMTP (v6)':
    dport    => 25,
    proto    => tcp,
    jump     => accept,
    protocol => 'IPv6',
  }
}
