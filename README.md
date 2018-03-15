# UMich Altmetric
Display Altmetric data using a wordpress shortcode.

### Shortcode
```
[altmetric url="{MY_URL_HERE}" limit="25" donut-size="small" template="default"]
```
OR
```
[altmetric limit="25" donut-size="small" template="default"]{MY_URL_HERE}[/altmetric]
```

#### Shortcode options
| Option     | Values        | Default |
| ---------- | ------------- | ------- |
| url        | string        |         |
| limit      | number        | 25      |
| donut-size | small, medium | medium  |
| template   | string        | default |
