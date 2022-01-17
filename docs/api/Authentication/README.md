# Authorization

## Get token

```bash
❯ curl \
  -s \
  -X POST \
  -H "Content-Type: application/json" \
  -d '{"email":"user1@domain.tld","password":"password1"}' \
  http://localhost/api/v1/token/get | jq .token
```

## With `$API_TOKEN`

```bash
❯ export API_TOKEN=$(bin/get-token user1@domain.tld password1)
❯ echo $API_TOKEN
```

### Request

```bash
❯ curl -X 'GET' -H 'Authorization: Bearer '$API_TOKEN \
    -s http://localhost/api/v1/calendars.json | jq .
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