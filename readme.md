# Installation

Install in your site using composer :

    {
        "name": "dhlab/vtm",
        "repositories": [
            {
                "type": "vcs",
                "url": "https://github.com/olivierdalang/mvc2000"
            }
        ],
        "require": {
            "olivierdalang/mvc2000": "dev-master"
        },
        "scripts": {
            "post-package-install": [
                "olivierdalang\\mvc2000\\Sys::postPackageInstall"
            ]
        }
    }

# Usage

A default site will be installed in the root folder.