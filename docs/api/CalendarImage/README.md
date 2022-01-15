# API examples CalendarImage - `v1`

**Endpoint**: http://localhost/api/v1/calendar_images.json

## Get collection (all)

```bash
❯ curl -X 'GET' -s http://localhost/api/v1/calendar_images.json | jq .
```

<details>
  <summary>Click to see example response</summary>

```json
[
  {
    "id": 1,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/1",
    "year": 2022,
    "month": 0
  },
  {
    "id": 2,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/2",
    "year": 2022,
    "month": 1
  },
  {
    "id": 3,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/3",
    "year": 2022,
    "month": 2
  },
  {
    "id": 4,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/4",
    "year": 2022,
    "month": 3
  },
  {
    "id": 5,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/5",
    "year": 2022,
    "month": 4
  },
  {
    "id": 6,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/6",
    "year": 2022,
    "month": 5
  },
  {
    "id": 7,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/7",
    "year": 2022,
    "month": 6
  },
  {
    "id": 8,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/8",
    "year": 2022,
    "month": 7
  },
  {
    "id": 9,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/9",
    "year": 2022,
    "month": 8
  },
  {
    "id": 10,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/10",
    "year": 2022,
    "month": 9
  },
  {
    "id": 11,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/11",
    "year": 2022,
    "month": 10
  },
  {
    "id": 12,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/12",
    "year": 2022,
    "month": 11
  },
  {
    "id": 13,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/13",
    "year": 2022,
    "month": 12
  }
]
```

</details>

## Get collection (filtered by calendar)

```bash
❯ curl -X 'GET' -s http://localhost/api/v1/calendars/1/calendar_images.json | jq .
```

<details>
  <summary>Click to see example response</summary>

```json
[
  {
    "id": 1,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/1",
    "year": 2022,
    "month": 0
  },
  {
    "id": 2,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/2",
    "year": 2022,
    "month": 1
  },
  {
    "id": 3,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/3",
    "year": 2022,
    "month": 2
  },
  {
    "id": 4,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/4",
    "year": 2022,
    "month": 3
  },
  {
    "id": 5,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/5",
    "year": 2022,
    "month": 4
  },
  {
    "id": 6,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/6",
    "year": 2022,
    "month": 5
  },
  {
    "id": 7,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/7",
    "year": 2022,
    "month": 6
  },
  {
    "id": 8,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/8",
    "year": 2022,
    "month": 7
  },
  {
    "id": 9,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/9",
    "year": 2022,
    "month": 8
  },
  {
    "id": 10,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/10",
    "year": 2022,
    "month": 9
  },
  {
    "id": 11,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/11",
    "year": 2022,
    "month": 10
  },
  {
    "id": 12,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/12",
    "year": 2022,
    "month": 11
  },
  {
    "id": 13,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/13",
    "year": 2022,
    "month": 12
  }
]
```

</details>

## Get collection (filtered by user)

```bash
❯ curl -X 'GET' -s http://localhost/api/v1/users/1/calendar_images.json | jq .
```

<details>
  <summary>Click to see example response</summary>

```json
[
  {
    "id": 1,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/1",
    "year": 2022,
    "month": 0
  },
  {
    "id": 2,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/2",
    "year": 2022,
    "month": 1
  },
  {
    "id": 3,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/3",
    "year": 2022,
    "month": 2
  },
  {
    "id": 4,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/4",
    "year": 2022,
    "month": 3
  },
  {
    "id": 5,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/5",
    "year": 2022,
    "month": 4
  },
  {
    "id": 6,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/6",
    "year": 2022,
    "month": 5
  },
  {
    "id": 7,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/7",
    "year": 2022,
    "month": 6
  },
  {
    "id": 8,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/8",
    "year": 2022,
    "month": 7
  },
  {
    "id": 9,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/9",
    "year": 2022,
    "month": 8
  },
  {
    "id": 10,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/10",
    "year": 2022,
    "month": 9
  },
  {
    "id": 11,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/11",
    "year": 2022,
    "month": 10
  },
  {
    "id": 12,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/12",
    "year": 2022,
    "month": 11
  },
  {
    "id": 13,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/13",
    "year": 2022,
    "month": 12
  }
]
```

</details>

## Get collection (filtered by user and calendar)

```bash
❯ curl -X 'GET' -s http://localhost/api/v1/users/1/calendars/1/calendar_images.json | jq .
```

<details>
  <summary>Click to see example response</summary>

```json
[
  {
    "id": 1,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/1",
    "year": 2022,
    "month": 0
  },
  {
    "id": 2,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/2",
    "year": 2022,
    "month": 1
  },
  {
    "id": 3,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/3",
    "year": 2022,
    "month": 2
  },
  {
    "id": 4,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/4",
    "year": 2022,
    "month": 3
  },
  {
    "id": 5,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/5",
    "year": 2022,
    "month": 4
  },
  {
    "id": 6,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/6",
    "year": 2022,
    "month": 5
  },
  {
    "id": 7,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/7",
    "year": 2022,
    "month": 6
  },
  {
    "id": 8,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/8",
    "year": 2022,
    "month": 7
  },
  {
    "id": 9,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/9",
    "year": 2022,
    "month": 8
  },
  {
    "id": 10,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/10",
    "year": 2022,
    "month": 9
  },
  {
    "id": 11,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/11",
    "year": 2022,
    "month": 10
  },
  {
    "id": 12,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/12",
    "year": 2022,
    "month": 11
  },
  {
    "id": 13,
    "user": "/api/v1/users/1",
    "calendar": "/api/v1/calendars/1",
    "image": "/api/v1/images/13",
    "year": 2022,
    "month": 12
  }
]
```

</details>

## Get item

```bash
❯ curl -X 'GET' -s http://localhost/api/v1/calendar_images/1.json | jq .
```

<details>
  <summary>Click to see example response</summary>

```json
{
  "id": 1,
  "user": "/api/v1/users/1",
  "calendar": "/api/v1/calendars/1",
  "image": "/api/v1/images/1",
  "year": 2022,
  "month": 0
}
```

</details>

## Get extended item

```bash
❯ curl -X 'GET' -s http://localhost/api/v1/calendar_images/1/extended.json | jq .
```

<details>
  <summary>Click to see example response</summary>

```json
{
  "id": 1,
  "user": "/api/v1/users/1",
  "calendar": "/api/v1/calendars/1",
  "image": "/api/v1/images/1",
  "year": 2022,
  "month": 0,
  "title": "Las Palmas, Gran Canaria, Spanien, 2021",
  "position": "28°09’42.9\"N 15°26’05.1\"W",
  "url": "https://www.google.de",
  "config": {
    "valign": 1
  }
}
```

</details>