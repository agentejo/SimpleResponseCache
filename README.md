Add files to `addons/SimpleResponseCache`.

# Configuration

Define cache duration (default is 60 sec) in `config/config.yaml`:

```
responseCache:
    duration: 60
```

# Usage

append `rspc=1` to your api calls you want to be cached:

```
/api/collection/get/{name}?token=*apitoken*&rspc=1
```


### 💐 SPONSORED BY

[![ginetta](https://user-images.githubusercontent.com/321047/29219315-f1594924-7eb7-11e7-9d58-4dcf3f0ad6d6.png)](https://www.ginetta.net)<br>
We create websites and apps that click with users.
