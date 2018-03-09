# UMich Altmetrics
Display Altmetrics data using a Wordpress shortcode.

You will need to generate an API URL in Altmetric Explorer. (Export Search Results -> Open Results in API) Please see Altmetric's support page for additional help: https://help.altmetric.com/support/solutions/articles/6000189064-exporting-search-results-opening-in-the-explorer-api/

### Shortcode
```
[altmetrics url="{MY_URL_HERE}" limit="25" donut-size="small" template="default"]
```
OR
```
[altmetrics limit="25" donut-size="small" template="default"]{MY_URL_HERE}[/altmetrics]
```

#### Shortcode options
| Option     | Values        | Default |
| ---------- | ------------- | ------- |
| url        | string        |         |
| limit      | number        | 25      |
| donut-size | small, medium | medium  |
| template   | string        | default |
