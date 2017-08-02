=== wp-spamspan ===
Contributors: chipr
Tags: spam, email
Requires at least: 2.9
Tested up to: 4.4.2
Stable tag: 1.2

Implements strong, automatic anti-spam protection for email addresses
that appear in the text of articles.

== Description ==

The "wp-spamspan" plugin implements strong, automatic anti-spam protection
for email addresses in content on a Wordpress site.  To spam crawling
'bots, email addresses appear in obfuscated form. To human visitors,
they appear as clickable links.

There are two parts to the wp-spamspan process.  First, the message
text is scanned for email addresses and they are rewritten into an
obfuscated form.

So, if you have a post that contains the text:

  chip@example.com

This plugin will rewrite that address to display as:

  chip [at] example [dot] com

A 'bot that harvests email addresses will see this text and won't be
able to extract a usable email address from it.

For human users, however, once the page is loaded into the web browser,
a Javascript procedure runs that locates the obfuscated email addresses
and converts them to clickable "mailto:" links for web browsers.

This plugin incorporates spamspan.js, which is licensed and published
separately (but bundled into this plugin distribution).  The home page
for spamspan.js is: http://www.spamspan.com/

== Installation ==

1. Upload to your plugins folder, usually `wp-content/plugins/`
1. Activate the plugin on the plugin screen

Anti-spam protection is automatically enabled when this plugin is activated.

== Frequently Asked Questions ==

= Will wp-spamspan hurt the accessibility of my web site for sight-impaired visitors? =

No. In fact, it's superior to some of the popular anti-spam techniques,
such as replacing email addresses with a graphic image.

wp-spamspan should be compatible with screen reading software. Worst
case, the sight impaired visitor should be presented with the
obfuscated-but-readable-and-understandable text email address.

= How strong is the spam protection? =

Pretty darn strong.  I've been using this technique on my web site for
serveral years, and it appears to be highly effective against against
'bot harvesting.

There are a lot of ineffective ways to protect email
addresses against spam.  I wrote an article back in 2003
(http://www.unicom.com/blog/entry/173) that talks about some of the
popular-but-ineffective ways to protect email addresses against 'bot
harvesting. Believe it or not, they are still commonly used today.

== Changelog ==

= 1.2 =
* No code changes. Just updated docs to indicate tested with WordPress 4.4.2. Changed licensing to Unlicense.

= 1.1 =
* No code changes. Just updated docs to indicate tested with WordPress 3.9.

= 1.0 =
* Release as stable.

= 0.3 =
* New administrative menu (Admin -> Plugins -> Spam Span) with setting to adjust filter priority.
* Avoid encoding address-looking-things in HTML tags.

= 0.2 =
* First posting on WordPress.org Plugins Directory.

= 0.1 =
* Initial release.

== Upgrade Notice ==

= 1.0 =
Significant rewrite since initial release. Recommend upgrading to this version.

= 0.1 =
Initial release.

== Bugs ==

= Javascript Assumption =

The SpamSpan protection would fail against an email harvesting 'bot that
contains a Javascript interpreter. Then the 'bot would receive the
decoded email address, just the way a web browser does.

Fortunately, it appears that most (if not all) current email 'bots
don't handle Javascript. So long as the time and effort of Javascript
processing exceeds the benefits of doing so, 'bot authors won't have a
lot of motivation to add that capability.

= Naive Email Recognition & Overly Aggresive Obfuscation =

The mechanism used to identify email addresses to obfuscate is somewhat
naive, and at times overly aggressive.

For instance, the URL for a shared, public Google calendar is:

http://www.google.com/calendar/ical/username@gmail.com/public/basic.ics

If I put that text in an article body then wp-spamspan will attempt to
convert the email address in the middle of that URL into an obfuscated
mailto: link.

The workaround is to replace the "@" at-sign with its "&#64;" HTML
character entith code, so you'd write the above as:

http://www.google.com/calendar/ical/username&#64;gmail.com/public/basic.ics

It's also possible that some complicated-but-valid email addresses may
not be recognized, and will appear without obfuscation.

= Failsafe =

(Note - this is a theoretical, architectural issue -- so don't let it
scare you off.)

One of the basic rules of security is that when a protection fails, you
want it to fail in a way that makes everything "safe". In the case of
wp-spamspan, that means that if the plugin stops working, you'd like it
if the email addresses would still be protected. Unfortunately, that's
not the case.

If, for instance, I accidentally twiddle a bit in the administration
control panel and disable the wp-spamspan plugin, all of the email
addresses on my site will now be exposed to address harvesting spambots.

This could be done (by munging email addresses before writing them to
the database), but probably not worth it. Plus that has its own set
of drawbacks.

I don't anticipate doing anything about this ... I just want you to
know that I tried to think through the disaster and attack scenarios
for this plugin.
