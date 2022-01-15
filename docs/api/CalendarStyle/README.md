# API examples CalendarStyle - `v1`

**Endpoint**: http://localhost/api/v1/calendar_styles.json

## Get collection (all)

```bash
❯ curl -X 'GET' -s http://localhost/api/v1/calendar_styles.json | jq .
```

<details>
  <summary>Click to see example response</summary>

```json
[
  {
    "id": 1,
    "name": "default",
    "config": {
      "name": "default"
    }
  }
]
```

</details>

## Get item

```bash
❯ curl -X 'GET' -s http://localhost/api/v1/calendar_styles/1.json | jq .
```

<details>
  <summary>Click to see example response</summary>

```json
{
  "id": 1,
  "name": "default",
  "config": {
    "name": "default"
  }
}
```

</details>
