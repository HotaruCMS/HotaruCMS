About LightVC
=============

LightVC is a lightweight [model-view-controller](http://en.wikipedia.org/wiki/Model-view-controller) (MVC) framework without the model. This decoupling allows any model or [object relation mapping](http://en.wikipedia.org/wiki/List_of_object-relational_mapping_software) (ORM) tool to be used.

LightVC's main purpose is to route requests to controller actions quickly and efficiently.  It aims to be extensible, allowing custom routers to be provided (e.g. map legacy URLs to their new locations).

Unlike other MVC frameworks, LightVC does not couple itself to other classes such as those for managing sessions, form helpers, and so on. This promotes code reuse by allowing existing code for such tasks be used.

LightVC has been in production use (millions of hits a month) since early 2007 and was released to the public under the FreeBSD license later that year.

Some project goals:

- Promote code re-use: allow usage of any model or ORM, session classes, helpers, etc.
- Be highly [configurable](docs/user_guide/configuration.md).
	- Don't like were things are supposed to be stored?  Change it.
	- Need a custom router that hits a DB for mapping legacy URLs?  Provide one.
	- Need multiple routers in a [chain-of-responsibility pattern](http://en.wikipedia.org/wiki/Chain-of-responsibility_pattern)?  No problem.
- Be PHP5 Strict compatible: detect problems in your own code by turning error reporting up and not having to worry about the core framework adding garbage to the logs.
- Have a small footprint:  code is a liability so have as little of it as possible (the core currently has ~600 lines of code and over 800 lines of phpDoc comments).
- Be Fast: the view-controller routing code should not be the bottleneck.


Installation
------------

1. Download and extract one of the [tagged releases](https://github.com/awbush/lightvc/tags).
2. Point a web server to the "webroot" folder.

Read more about this in the [Quickstart Guide](docs/quickstart_guide.md).


Support
-------

- [Read documentation](docs/index.md) or dive right into the example controllers, views, and application config in the download.
- [View the changelog](CHANGELOG.md).
- [Report issues](https://github.com/awbush/lightvc/issues) on github.
- [Use pull requests](https://help.github.com/articles/using-pull-requests/) to contribute code.
- The deprecated launchpad site can still be found [here](https://launchpad.net/lightvc).
