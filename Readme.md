# TYPO3 Extension: Page Language

Set the language fallback type via page field.

Before TYPO3 9.5 Site Handling it was possible to use something like

```
[globalVar = TSFE:id=1] && [globalVar = GP:L=2]
    config {
        sys_language_mode = ignore
    }
[global]
```

to have an individual language handling.

Now it's unfortunately done globally in site language configuration

```
languages:
    -
        title: de-DE
        fallbackType: fallback
        languageId: '1'
        ...
```

See also https://forge.typo3.org/issues/86712
