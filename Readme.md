# TYPO3 Extension: Page Language

Set the language fallback type via page field.

![pagefield](https://github.com/networkteam/pagelanguage/raw/master/Documentation/Images/pagefield-fallbacktype.png "New field in page translation")

Before TYPO3 9.5 Site Handling it was possible to use something like

```
[globalVar = TSFE:id=1] && [globalVar = GP:L=2]
    config {
        sys_language_mode = ignore
    }
[global]
```

to have an individual language handling. See also https://forge.typo3.org/issues/86712
