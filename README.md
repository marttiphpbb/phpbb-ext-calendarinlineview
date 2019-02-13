# phpBB Extension - marttiphpbb Calendar Inline View

[Topic on phpbb.com](https://www.phpbb.com/community/viewtopic.php?f=456&t=2502596)

## Description

This phpBB extension provides a Calendar Inline View for the [Calendar extension set](https://github.com/marttiphpbb/phpbb-ext-calendarmono/blob/master/doc/calendar-set.md)

## Screenshot

![Index](doc/index.png)

## Requirements

* phpBB 3.2.5+
* PHP 7.1+
* phpBB extension [Calendar Mono](https://github.com/marttiphpbb/phpbb-ext-calendarmono)
* phpBB extension [Overall Page Blocks](https://github.com/marttiphpbb/phpbb-ext-overallpageblocks)
* PHP calendar extension (PHP compiled with --enable-calendar)

When it's not possible for you to have PHP compiled with --enable-calendar see the [PHP Ext Calendar extension](https://github.com/marttiphpbb/phpbb-ext-phpextcalendar) for a solution.

## Quick Install

You can install this on the latest release of phpBB 3.2 by following the steps below:

* Create `marttiphpbb/calendarinlineview` in the `ext` directory.
* Download and unpack the repository into `ext/marttiphpbb/calendarinlineview`
* Enable `Calendar Inline View` in the ACP at `Customise -> Manage extensions`.

## Uninstall

* Disable `Calendar Inline View` in the ACP at `Customise -> Extension Management -> Extensions`.
* To permanently uninstall, click `Delete Data`. Optionally delete the `/ext/marttiphpbb/calendarinlineview` directory.

## Support

* Report bugs and other issues to the [Issue Tracker](https://github.com/marttiphpbb/phpbb-ext-calendarinlineview/issues).

## License

[GPL-2.0](license.txt)

## More Screenshots (ACP)

![Index](doc/acp_index.png)

![Index](doc/acp_forums.png)

![Index](doc/acp_rendering.png)
