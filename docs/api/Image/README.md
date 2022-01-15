# API examples Image - `v1`

**Endpoint**: http://localhost/api/v1/images.json

## Get collection (all)

```bash
❯ curl -X 'GET' -s http://localhost/api/v1/images.json | jq .
```

<details>
  <summary>Click to see example response</summary>

```json
[
  {
    "id": 1,
    "user": "/api/users/1",
    "path": "source/00.jpg",
    "width": 4631,
    "height": 3165,
    "size": 10449328,
    "calendarImages": [
      "/api/calendar_images/1"
    ]
  },
  {
    "id": 2,
    "user": "/api/users/1",
    "path": "source/01.jpg",
    "width": 3982,
    "height": 2655,
    "size": 5545456,
    "calendarImages": [
      "/api/calendar_images/2"
    ]
  },
  {
    "id": 3,
    "user": "/api/users/1",
    "path": "source/02.jpg",
    "width": 5287,
    "height": 3553,
    "size": 17329561,
    "calendarImages": [
      "/api/calendar_images/3"
    ]
  },
  {
    "id": 4,
    "user": "/api/users/1",
    "path": "source/03.jpg",
    "width": 5895,
    "height": 3930,
    "size": 15620127,
    "calendarImages": [
      "/api/calendar_images/4"
    ]
  },
  {
    "id": 5,
    "user": "/api/users/1",
    "path": "source/04.jpg",
    "width": 6000,
    "height": 4000,
    "size": 19157691,
    "calendarImages": [
      "/api/calendar_images/5"
    ]
  },
  {
    "id": 6,
    "user": "/api/users/1",
    "path": "source/05.jpg",
    "width": 6000,
    "height": 4000,
    "size": 7250879,
    "calendarImages": [
      "/api/calendar_images/6"
    ]
  },
  {
    "id": 7,
    "user": "/api/users/1",
    "path": "source/06.jpg",
    "width": 6000,
    "height": 4000,
    "size": 15530621,
    "calendarImages": [
      "/api/calendar_images/7"
    ]
  },
  {
    "id": 8,
    "user": "/api/users/1",
    "path": "source/07.jpg",
    "width": 6000,
    "height": 4000,
    "size": 6981313,
    "calendarImages": [
      "/api/calendar_images/8"
    ]
  },
  {
    "id": 9,
    "user": "/api/users/1",
    "path": "source/08.jpg",
    "width": 5949,
    "height": 3966,
    "size": 18109000,
    "calendarImages": [
      "/api/calendar_images/9"
    ]
  },
  {
    "id": 10,
    "user": "/api/users/1",
    "path": "source/09.jpg",
    "width": 6000,
    "height": 4000,
    "size": 6265714,
    "calendarImages": [
      "/api/calendar_images/10"
    ]
  },
  {
    "id": 11,
    "user": "/api/users/1",
    "path": "source/10.jpg",
    "width": 6000,
    "height": 4000,
    "size": 11329505,
    "calendarImages": [
      "/api/calendar_images/11"
    ]
  },
  {
    "id": 12,
    "user": "/api/users/1",
    "path": "source/11.jpg",
    "width": 6000,
    "height": 4000,
    "size": 16552192,
    "calendarImages": [
      "/api/calendar_images/12"
    ]
  },
  {
    "id": 13,
    "user": "/api/users/1",
    "path": "source/12.jpg",
    "width": 6000,
    "height": 4000,
    "size": 9436293,
    "calendarImages": [
      "/api/calendar_images/13"
    ]
  }
]
```

</details>

## Get collection (filtered by user)

```bash
❯ curl -X 'GET' -s http://localhost/api/v1/users/1/images.json | jq .
```

<details>
  <summary>Click to see example response</summary>

```json
[
  {
    "id": 1,
    "user": "/api/v1/users/1",
    "path": "source/00.jpg",
    "width": 4631,
    "height": 3165,
    "size": 10449328,
    "calendarImages": [
      "/api/v1/calendar_images/1"
    ]
  },
  {
    "id": 2,
    "user": "/api/v1/users/1",
    "path": "source/01.jpg",
    "width": 3982,
    "height": 2655,
    "size": 5545456,
    "calendarImages": [
      "/api/v1/calendar_images/2"
    ]
  },
  {
    "id": 3,
    "user": "/api/v1/users/1",
    "path": "source/02.jpg",
    "width": 5287,
    "height": 3553,
    "size": 17329561,
    "calendarImages": [
      "/api/v1/calendar_images/3"
    ]
  },
  {
    "id": 4,
    "user": "/api/v1/users/1",
    "path": "source/03.jpg",
    "width": 5895,
    "height": 3930,
    "size": 15620127,
    "calendarImages": [
      "/api/v1/calendar_images/4"
    ]
  },
  {
    "id": 5,
    "user": "/api/v1/users/1",
    "path": "source/04.jpg",
    "width": 6000,
    "height": 4000,
    "size": 19157691,
    "calendarImages": [
      "/api/v1/calendar_images/5"
    ]
  },
  {
    "id": 6,
    "user": "/api/v1/users/1",
    "path": "source/05.jpg",
    "width": 6000,
    "height": 4000,
    "size": 7250879,
    "calendarImages": [
      "/api/v1/calendar_images/6"
    ]
  },
  {
    "id": 7,
    "user": "/api/v1/users/1",
    "path": "source/06.jpg",
    "width": 6000,
    "height": 4000,
    "size": 15530621,
    "calendarImages": [
      "/api/v1/calendar_images/7"
    ]
  },
  {
    "id": 8,
    "user": "/api/v1/users/1",
    "path": "source/07.jpg",
    "width": 6000,
    "height": 4000,
    "size": 6981313,
    "calendarImages": [
      "/api/v1/calendar_images/8"
    ]
  },
  {
    "id": 9,
    "user": "/api/v1/users/1",
    "path": "source/08.jpg",
    "width": 5949,
    "height": 3966,
    "size": 18109000,
    "calendarImages": [
      "/api/v1/calendar_images/9"
    ]
  },
  {
    "id": 10,
    "user": "/api/v1/users/1",
    "path": "source/09.jpg",
    "width": 6000,
    "height": 4000,
    "size": 6265714,
    "calendarImages": [
      "/api/v1/calendar_images/10"
    ]
  },
  {
    "id": 11,
    "user": "/api/v1/users/1",
    "path": "source/10.jpg",
    "width": 6000,
    "height": 4000,
    "size": 11329505,
    "calendarImages": [
      "/api/v1/calendar_images/11"
    ]
  },
  {
    "id": 12,
    "user": "/api/v1/users/1",
    "path": "source/11.jpg",
    "width": 6000,
    "height": 4000,
    "size": 16552192,
    "calendarImages": [
      "/api/v1/calendar_images/12"
    ]
  },
  {
    "id": 13,
    "user": "/api/v1/users/1",
    "path": "source/12.jpg",
    "width": 6000,
    "height": 4000,
    "size": 9436293,
    "calendarImages": [
      "/api/v1/calendar_images/13"
    ]
  }
]
```

</details>

## Get item

```bash
❯ curl -X 'GET' -s http://localhost/api/v1/images/1.json | jq .
```

<details>
  <summary>Click to see example response</summary>

```json
{
  "id": 1,
  "user": "/api/v1/users/1",
  "path": "source/00.jpg",
  "width": 4631,
  "height": 3165,
  "size": 10449328,
  "calendarImages": [
    "/api/v1/calendar_images/1"
  ]
}
```

</details>
