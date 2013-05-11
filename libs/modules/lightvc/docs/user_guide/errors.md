Errors
======

LightVC will throw an `Lvc_Exception` when it runs into trouble:

* The controller can not be found.
* The action can not be run.
* The controller view can not be found.
* The layout view can not be found.

The only error that does not result in a thrown exception is when an element can not be loaded.  Instead, LightVC logs the error and continues execution.
