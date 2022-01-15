# API examples HolidayGroup - `v1`

**Endpoint**: http://localhost/api/v1/holiday_groups.json

## Get collection (all)

```bash
❯ curl -X 'GET' -s http://localhost/api/v1/holiday_groups.json | jq .
```

<details>
  <summary>Click to see example response</summary>

```json
[
  {
    "id": 1,
    "name": "Saxony",
    "holidays": [
      "/api/holidays/1",
      "/api/holidays/2",
      "/api/holidays/3",
      "/api/holidays/4",
      "/api/holidays/5",
      "/api/holidays/6",
      "/api/holidays/7",
      "/api/holidays/8",
      "/api/holidays/9",
      "/api/holidays/10",
      "/api/holidays/11"
    ]
  }
]
```

</details>

## Get item

```bash
❯ curl -X 'GET' -s http://localhost/api/v1/holiday_groups/1.json | jq .
```

<details>
  <summary>Click to see example response</summary>

```json
{
  "id": 1,
  "name": "Saxony",
  "holidays": [
    "/api/v1/holidays/1",
    "/api/v1/holidays/2",
    "/api/v1/holidays/3",
    "/api/v1/holidays/4",
    "/api/v1/holidays/5",
    "/api/v1/holidays/6",
    "/api/v1/holidays/7",
    "/api/v1/holidays/8",
    "/api/v1/holidays/9",
    "/api/v1/holidays/10",
    "/api/v1/holidays/11"
  ]
}
```

</details>
