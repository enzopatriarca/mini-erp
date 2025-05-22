<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title','Mini ERP')</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-light bg-light px-3">
    <a class="navbar-brand" href="{{ route('produtos.index') }}">Mini ERP</a>
  </nav>

  <main class="container py-4">
    @if(session('status'))
      <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @yield('content')
  </main>
</body>
</html>
