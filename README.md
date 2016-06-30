# API examples
Example code for Myracloud API usage.

Prerequisits:
    php composer.phar install

Usage:

    bin/console
    Console Tool

    Usage:
      command [options] [arguments]

    Options:
      -h, --help            Display this help message
      -q, --quiet           Do not output any message
      -V, --version         Display this application version
          --ansi            Force ANSI output
          --no-ansi         Disable ANSI output
      -n, --no-interaction  Do not ask any interactive question
      -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

    Available commands:
      help                       Displays help for a command
      list                       Lists commands
     myracloud
      myracloud:api:cacheClear   CacheClear commands allows you to do a cache clear via Myracloud API.
      myracloud:api:errorPages   The errorPages command allows you to set error pages.
      myracloud:api:maintenance  The maintenance command allows you to list, create, update, and delete maintenace pages.
