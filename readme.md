PHP-Events
==========

This project was put together really quickly and has not had a lot of effort put in.
As a result of the lack of effort, there is no security around this app... and barely any features.
It is simply a frontend for a CSV file.

One day I will spend some time on this and build a proper web app to do the same thing.


The goal
--------

To provide a place where people can put their name against which dates they are free for from a pre-defined list.


Setup
-----

* Clone this repository to a web server
* Configure the CSV file
    * Copy dates.csv.example to date.csv and open it in an editor
    * Set the first line as a description that will appear at the top of the page
    * Set the subsequent lines with timestamps in the format: YYYYMMDDhhmmss
* Ensure dates.csv is writable by the server
