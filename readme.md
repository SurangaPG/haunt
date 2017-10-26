# Haunt (WIP)

Note that you'll need imagemagick installed.

Note that the implementation is currently being ported. 

Haunt is a php equivalent for wraith to prevent having too many different
dependencies on your testing servers. It's main purpose is to provide 
a very low effort way of checking functionallity and layout for 
continuous integration projects. 

## Key concepts
The main idea is to keep the various components of the tool as autonomous
as possible. So it can support as many sources as possible. The basic 
command (compare) allows you to compare any sets of files on your filesystem
regardless of the source. 

### Providing screenshots
Currently a basic implementation for the taking of screenshots has been 
provider using a selenium2 (tested with the official docker image) instance. 
Temporary docker container should suffice: 
``` 
 docker run -p 4444:4444 -p 5900:5900 --rm selenium/standalone-firefox-debug:2.53.1-beryllium
``` 
See https://github.com/SeleniumHQ/docker-selenium

It requires a --config options pointing to a simple .yml file (currently only accepts an absolute path). 
See test/config.yml for an example.

And a domain to visit e.g https://stedelijkonderwijs.be. 

Optionally an output dir can be provided.

To switch output file use the --target=new or --target=baseline options.

``` 
./path/to/bin snapshots:selenium --config=/PATH/TO/CONFIG/FILE.yml --domain=https://stedelijkonderwijs.be
```
Or have this running on port 4444 some other way.

Initially you'll have to run this twice once with domain for the the baseline content (for example production).
Once with the domain for comparison (for example acc). 

Afterwards you'll have a similar set of files:

``` 
 /OUTPUTPATH
    /group-name
        /0
            - _haunt-info.yml
            - baseline.png
            - new.png
        /1
            - _haunt-info.yml
            - baseline.png
            - new.png
        /2
            - _haunt-info.yml
            - baseline.png
            - new.png
``` 

Note that this is just a base implementation, any screenshots in this 
filesystem structure are valid. 

More groups can be added in the same way. 

### Comparing screenshots
To compare screenshots the following dir structure is required.

``` 
    WHEREVER/group-name
                /screenshot-set-name
                    - baseline.png
                    - new.png
```

Afterwards you can run haunt and a new file "diff.png" will be added highlighting 
the differences between the 2 files. 
Also a report.json file will have been made in the source dir (this can 
then be uses by any output generator). 

``` 
    ./vendor/bin/haunt compare --source=WHEREVER
```

## Statis html generator 
As a base implementation a very basic POC for a static html generator 
has been added. (Will be expanded further). 

``` 
    ./vendor/bin/haunt generate-html --source=/ABSOLUTE/PATH/TO/REPORT.json --target=/ABSOLUTE/PATH/TO/OUTPUT/DIR
```

This will generate a set of (currently very basic html files to display
the screenshots.

## Roadmap

- Allow temporary paths
- Add integration for gherkin based making of screenshots (behat)
- Improve UI for the results
- Improve report handling
- Make dependency on imagemagick clearer