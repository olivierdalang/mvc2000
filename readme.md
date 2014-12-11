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
        }        
    }

Then run

    composer install


# Usage

To create a site skeletton, copy the `vendor/olivierdalang/mvc2000/default_project` folder to your project root :

    cp -ri vendor/olivierdalang/mvc2000/default_project/. ./

To have this step made automatically upon install, you can add this to your composer.json file :

    "scripts": {
        "post-install-cmd": [
            "echo 'mvc200 : installing web app skelettoninstalling web app skeletton';cp -rn vendor/olivierdalang/mvc2000/default_project/. ./"
        ]
    }

