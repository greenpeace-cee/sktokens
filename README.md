# sktokens

![Screenshot](/images/screenshot.png)

Build your own tokens with SearchKit.

The extension is licensed under [AGPL-3.0](LICENSE.txt).

## Requirements

* PHP v7.4+
* CiviCRM 5.60+

## Installation (Web UI)

Learn more about installing CiviCRM extensions in the [CiviCRM Sysadmin Guide](https://docs.civicrm.org/sysadmin/en/latest/customize/extensions/).

## Installation (CLI, Zip)

Sysadmins and developers may download the `.zip` file for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
cd <extension-dir>
cv dl sktokens@https://github.com/FIXME/sktokens/archive/master.zip
```
or
```bash
cd <extension-dir>
cv dl sktokens@https://lab.civicrm.org/extensions/sktokens/-/archive/main/sktokens-main.zip
```

## Installation (CLI, Git)

Sysadmins and developers may clone the [Git](https://en.wikipedia.org/wiki/Git) repo for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
git clone https://github.com/FIXME/sktokens.git
cv en sktokens
```
or
```bash
git clone https://lab.civicrm.org/extensions/sktokens.git
cv en sktokens
```

## Getting Started

* Create a SearchKit using the entity you'd like the tokens to use (usually this will be Contacts).
* Add any columns you'd like to the SearchKit.
* Create a new display of type "Tokens" and save.

Now, when creating a new email/letter, the tokens should appear in your token browser under a category matching your display name.

## Possible future features

* Add rewrite option similar to table/list displays.
* Add option for HTML output.
