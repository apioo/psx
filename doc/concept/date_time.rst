
Date time
=========

Abstract
--------

PSX has a unified way to handle date, time and date time data. This chapter 
explains short how this works.

Usage
-----

In general PSX follows :rfc:`3339#section-5.6` which is a subset of ISO 8601.
There are three date time property types which can be used in your request or 
response schema:

* Date [full-date] (``2015-05-02``)
* Time [full-time] (``10:51:12``)
* DateTime [full-date "T" full-time] (``2015-05-02T10:51:12Z``)

All incoming and outgoing data must be formatted according to the 
:rfc:`3339#section-5.6` format otherwise the value gets not accepted. For 
outgoing data there is a small exception which also allows to pass an mysql date 
time format ``2015-05-02 10:51:12`` as date time value. PSX will transform this 
value to ``2015-05-02T10:51:12Z``. In the end that means that your API consumes 
and returns only :rfc:`3339#section-5.6` compatible date time formats.
