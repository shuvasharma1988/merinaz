@extends('layouts.error')

@section('title', 'Page Not Found')
@section('code_label', 'Error 404')
@section('heading', 'This page was not found.')
@section('message', 'It may have been moved or the link is old. Check the menu and try again.')

@section('actions')
    <a href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : url('/') }}" class="btn btn-accent btn-sm">
        Go to today
    </a>
@endsection
```

---

### 3. 403 Page (Forbidden / Role Access Denied)
**File path:** `resources/views/pages/error/403.blade.php`[cite: 1, 2]

```html

```

---

### 4. 419 Page (Page Expired / CSRF Token Mismatch)
**File path:** `resources/views/pages/error/419.blade.php`[cite: 1, 2]

```html
@extends('layouts.error')

@section('title', 'Session Expired')
@section('code_label', 'Error 419')
@section('heading', 'Your session expired.')
@section('message', 'Log in again to continue. Unsaved work was kept on this device.')


```

---

### 5. 429 Page (Too Many Requests / Rate Limited)
**File path:** `resources/views/pages/error/429.blade.php`[cite: 1, 2]

```html

```

---

### 6. 500 Page (Internal Server Error)
**File path:** `resources/views/pages/error/500.blade.php`[cite: 1, 2]

```html

```

---

### 7. 503 Page (Service Unavailable / Maintenance Mode)
**File path:** `resources/views/pages/error/503.blade.php`[cite: 1, 2]

```html

```
