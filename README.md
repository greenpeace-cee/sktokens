# SearchKit Tokens
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

* Create a SearchKit (**Search menu » SearchKit**). The entity should match the search type you intend to use - e.g. "Contacts" for *Advanced Search*, "Contribution" for *Find Contributions*, etc.
* Add any columns you'd like to use as tokens. Feel free to use field transformations, grouping, etc.
* Create a new display of type "Tokens" (see screenshot 1).
* Place a label you'd like to see in your token browser under each field you'd like to use as a token (see screenshot 2).
* When performing an action that uses tokens - emails, letters, receipts and more - select the token from the browser.

Now, when creating a new email/letter, the tokens should appear in your token browser under a category matching your display name (see screenshot 3).

**Screenshot 1**
![Screenshot](/images/Selection_1859.png)

**Screenshot 2**
![Screenshot](/images/Selection_1860.png)

**Screenshot 3**
![Screenshot](/images/Selection_1861.png)

## Possible future features

* Add rewrite option similar to table/list displays.
* Add option for HTML output.
