# Introduction

Provide an easy way to generate a PDF for any page you have.

# Setup

Install the extension as usual. Include the static TypoScript where you need
paged to be generated as PDF.

Call the given page with the configured `type=` parameter.

# Configuration

The extension is configured via TS only. Everything is inside the new page
configuration `pdf`.

If the preconfigured `type 100` is already in use in your installation, just
overwrite it to your needs.

Everything is configured with defaults and can be overwritten via TypoScript.

Each option is documented inside the TS, just browse through it using the
constant editor with further documentation.

# Security

This extension will do a system call to generate the PDFs. While doing so, some
information need to be passed to the shell. This can lead to security issues.
Every part of the command is escaped with PHP native functionality.

Anyway, take care what you configure and what is passed to the shell.

Limit the configuration to admins or some respected group, as you always should
to with TypoScript.

# Current state

NOTE: The state is still alpha! This means:

The extension can behave unexpected in many cases. E.g. all generated files are
stored with md5 hash of the given url in one folder which can lead to many files
if you don't remove old ones.

We will add such things later, but most of them are very easy to extend. So do
it your own and bring back the efforts to others. Send in Pull Requests /
patches to the author

We will add such things later, but most of them are very easy to extend. So do
it your own and bring back the efforts to others. Send in Pull Requests /
patches to the author.