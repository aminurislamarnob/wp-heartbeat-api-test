### For dev. environment

Run the following command for development environment.

```
composer update
```

### For production environment

Run the following command for production environment to ignore the dev dependencies.

```
composer update --no-dev
```

### Build Release

Set execution permission to the script file by `chmod +x bin/build.sh` command. Now, Run the following bash script.

```
bin/build.sh
```

### Shortcode

Use this shortcode `[welabs_chatbox]` to any page to view simple chatbox.

### Resources

1. https://developer.wordpress.org/plugins/javascript/heartbeat-api/
2. https://stackoverflow.com/questions/48255388/how-to-reset-heartbeat-time-in-wordpress-after-ajax
3. https://code.tutsplus.com/the-heartbeat-api-changing-the-pulse--wp-32462t
