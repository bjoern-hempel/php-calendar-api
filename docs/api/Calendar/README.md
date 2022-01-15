# API examples Calendar - `v1`

**Endpoint**: http://localhost/api/v1/calendars.json

## Get collection (all)

```bash
❯ curl -X 'GET' -s http://localhost/api/v1/calendars.json | jq .
```

<details>
  <summary>Click to see example response</summary>

```json
[
  {
    "id": 1,
    "user": "/api/v1/users/1",
    "calendar_style": "/api/v1/calendar_styles/1",
    "name": "Calendar 1",
    "title": "2022",
    "subtitle": "With love - Isa & Björn",
    "holiday_group": "/api/v1/holiday_groups/1",
    "config": {
      "background-color": "255,255,255,100",
      "print-calendar-week": true,
      "print-week-number": true,
      "print-qr-code-month": true,
      "print-qr-code-title": true,
      "aspect-ratio": 1.414,
      "height": 4000
    }
  }
]
```

</details>

## Get collection (filtered by user)

```bash
❯ curl -X 'GET' -s http://localhost/api/v1/users/1/calendars.json | jq .
```

<details>
  <summary>Click to see example response</summary>

```json
[
  {
    "id": 1,
    "user": "/api/v1/users/1",
    "calendar_style": "/api/v1/calendar_styles/1",
    "name": "Calendar 1",
    "title": "2022",
    "subtitle": "With love - Isa & Björn",
    "holiday_group": "/api/v1/holiday_groups/1",
    "config": {
      "background-color": "255,255,255,100",
      "print-calendar-week": true,
      "print-week-number": true,
      "print-qr-code-month": true,
      "print-qr-code-title": true,
      "aspect-ratio": 1.414,
      "height": 4000
    }
  }
]
```

</details>

## Get item

```bash
❯ curl -X 'GET' -s http://localhost/api/v1/calendars/1.json | jq .
```

<details>
  <summary>Click to see example response</summary>

```json
{
  "id": 1,
  "user": "/api/v1/users/1",
  "calendar_style": "/api/v1/calendar_styles/1",
  "name": "Calendar 1",
  "title": "2022",
  "subtitle": "With love - Isa & Björn",
  "holiday_group": "/api/v1/holiday_groups/1",
  "config": {
    "background-color": "255,255,255,100",
    "print-calendar-week": true,
    "print-week-number": true,
    "print-qr-code-month": true,
    "print-qr-code-title": true,
    "aspect-ratio": 1.414,
    "height": 4000
  }
}
```

</details>

## Get extended item

```bash
❯ curl -X 'GET' -s http://localhost/api/v1/calendars/1/extended.json | jq .
```

<details>
  <summary>Click to see example response</summary>

```json
{
  "user": "/api/v1/users/1",
  "calendar_style": "/api/v1/calendar_styles/1",
  "name": "Calendar 1",
  "title": "2022",
  "subtitle": "With love - Isa & Björn",
  "holiday_group": "/api/v1/holiday_groups/1",
  "calendarImages": [
    "/api/v1/calendar_images/1",
    "/api/v1/calendar_images/2",
    "/api/v1/calendar_images/3",
    "/api/v1/calendar_images/4",
    "/api/v1/calendar_images/5",
    "/api/v1/calendar_images/6",
    "/api/v1/calendar_images/7",
    "/api/v1/calendar_images/8",
    "/api/v1/calendar_images/9",
    "/api/v1/calendar_images/10",
    "/api/v1/calendar_images/11",
    "/api/v1/calendar_images/12",
    "/api/v1/calendar_images/13"
  ],
  "config": {
    "background-color": "255,255,255,100",
    "print-calendar-week": true,
    "print-week-number": true,
    "print-qr-code-month": true,
    "print-qr-code-title": true,
    "aspect-ratio": 1.414,
    "height": 4000
  }
}
```

</details>
