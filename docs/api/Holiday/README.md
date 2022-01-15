# API examples Holiday - `v1`

**Endpoint**: http://localhost/api/v1/holidays.json

## Get collection (all)

```bash
❯ curl -X 'GET' -s http://localhost/api/v1/holidays.json | jq .
```

<details>
  <summary>Click to see example response</summary>

```json
[
  {
    "id": 1,
    "holiday_group": "/api/holiday_groups/1",
    "name": "Neujahr",
    "date": "2022-01-01T00:00:00+00:00",
    "config": {
      "color": "255,255,255,100"
    }
  },
  {
    "id": 2,
    "holiday_group": "/api/holiday_groups/1",
    "name": "Karfreitag",
    "date": "2022-04-15T00:00:00+00:00",
    "config": {
      "color": "255,255,255,100"
    }
  },
  {
    "id": 3,
    "holiday_group": "/api/holiday_groups/1",
    "name": "Ostern",
    "date": "2022-04-18T00:00:00+00:00",
    "config": {
      "color": "255,255,255,100"
    }
  },
  {
    "id": 4,
    "holiday_group": "/api/holiday_groups/1",
    "name": "1. Mai",
    "date": "2022-05-01T00:00:00+00:00",
    "config": {
      "color": "255,255,255,100"
    }
  },
  {
    "id": 5,
    "holiday_group": "/api/holiday_groups/1",
    "name": "Christi Himmelfahrt",
    "date": "2022-05-26T00:00:00+00:00",
    "config": {
      "color": "255,255,255,100"
    }
  },
  {
    "id": 6,
    "holiday_group": "/api/holiday_groups/1",
    "name": "Pfingsten ",
    "date": "2022-06-06T00:00:00+00:00",
    "config": {
      "color": "255,255,255,100"
    }
  },
  {
    "id": 7,
    "holiday_group": "/api/holiday_groups/1",
    "name": "Tag der Deutschen Einheit",
    "date": "2022-10-03T00:00:00+00:00",
    "config": {
      "color": "255,255,255,100"
    }
  },
  {
    "id": 8,
    "holiday_group": "/api/holiday_groups/1",
    "name": "Reformationstag",
    "date": "2022-10-31T00:00:00+00:00",
    "config": {
      "color": "255,255,255,100"
    }
  },
  {
    "id": 9,
    "holiday_group": "/api/holiday_groups/1",
    "name": "Buß- und Bettag",
    "date": "2022-11-16T00:00:00+00:00",
    "config": {
      "color": "255,255,255,100"
    }
  },
  {
    "id": 10,
    "holiday_group": "/api/holiday_groups/1",
    "name": "1. Weihnachtsfeiertag",
    "date": "2022-12-25T00:00:00+00:00",
    "config": {
      "color": "255,255,255,100"
    }
  },
  {
    "id": 11,
    "holiday_group": "/api/holiday_groups/1",
    "name": "2. Weihnachtsfeiertag",
    "date": "2022-12-26T00:00:00+00:00",
    "config": {
      "color": "255,255,255,100"
    }
  }
]
```

</details>

## Get item

```bash
❯ curl -X 'GET' -s http://localhost/api/v1/holidays/1.json | jq .
```

<details>
  <summary>Click to see example response</summary>

```json
{
  "id": 1,
  "holiday_group": "/api/v1/holiday_groups/1",
  "name": "Neujahr",
  "date": "2022-01-01T00:00:00+00:00",
  "config": {
    "color": "255,255,255,100"
  }
}
```

</details>
