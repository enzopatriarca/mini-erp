<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title') – Mini ERP</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    :root {
      --bs-primary: #6f42c1;
      --bs-secondary: #343a40;
      --bs-light:    #f8f9fa;
      --bs-gradient-start: #4e73df;
      --bs-gradient-end:   #6f42c1;
    }
    html, body { height:100%; margin:0; }
    body {
      display:flex;
      font-family:'Segoe UI',sans-serif;
      transition:all .3s ease;
    }

    /* SIDEBAR */
    #sidebar {
      display:flex;
      flex-direction:column;
      box-sizing:border-box;
      width:220px;
      padding:1rem;
      background:linear-gradient(180deg, var(--bs-gradient-start), var(--bs-gradient-end));
      color:#fff;
      flex-shrink:0;
      overflow:hidden;
      transition:width .3s ease, padding .3s ease;
    }
    #sidebar .sidebar-header {
      display:flex;
      align-items:center;
      padding:.5rem 1rem;
      margin-bottom:1rem;
      color:rgba(255,255,255,0.85);
      text-decoration:none;
    }
    #sidebar a.nav-link {
      display:flex;
      align-items:center;
      padding:.5rem 1rem;
      color:rgba(255,255,255,0.85);
      text-decoration:none;
      margin-bottom:.25rem;
    }
    #sidebar .nav-link:hover,
    #sidebar .nav-link.active {
      background:rgba(255,255,255,0.15);
      color:#fff;
    }
    #sidebar .icon { font-size:1.2rem; }
    #sidebar .label { margin-left:.5rem; }

    /* CONTEÚDO */
    #content {
      flex-grow:1;
      display:flex;
      flex-direction:column;
      min-height:100%;
      background:#fff;
      transition:margin .3s ease;
    }
    #inner-header {
      display:flex;
      align-items:center;
      padding:.75rem 1.5rem;
      border-bottom:1px solid #eaeaea;
    }
    #btnToggleSidebar {
      font-size:1.25rem;
      background:none;
      border:none;
      margin-right:1rem;
      cursor:pointer;
      color:var(--bs-secondary);
    }
    main {
      flex:1;
      padding:2rem;
      background:#f1f3f5;
    }
    footer {
      position: fixed;
      bottom: 0;
      left: 0;
      width: 100%;
      z-index: 9999;
      background: var(--bs-light);
    }
    #content, main { padding-bottom: 3.5rem; }

    .toast-container { z-index:10000 !important; }

    .btn-primary {
      background-color:var(--bs-primary);
      border-color:var(--bs-primary);
    }
    .btn-primary:hover {
      background-color:#5a32a3;
      border-color:#5a32a3;
    }
    .btn-secondary {
      background-color:var(--bs-secondary);
      border-color:var(--bs-secondary);
    }
    .badge-secondary {
      background-color:var(--bs-secondary);
    }

    /* SIDEBAR COLLAPSED */
    body.sidebar-collapsed #sidebar {
      width:60px !important;
      padding:.5rem !important;
    }
    body.sidebar-collapsed #sidebar .label {
      display:none !important;
    }
    body.sidebar-collapsed #sidebar .nav-link {
      justify-content:center;
      padding:.5rem 0 !important;
    }
    body.sidebar-collapsed #content {
      margin-left:0;
    }
  </style>
</head>
<body>
  {{-- SIDEBAR --}}
  <nav id="sidebar">
    <a href="{{ route('produtos.index') }}" class="sidebar-header">
      <i class="bi bi-gear-fill icon"></i>
      <span class="label fs-4 fw-bold">Mini ERP</span>
    </a>

    @php
      // Conta total de itens no carrinho (soma das quantidades)
      $cartItems = session('carrinho', []);
      $cartCount = collect($cartItems)->sum('quantidade');
    @endphp

    <ul class="nav nav-pills flex-column mb-auto">
      <li class="nav-item">
        <a href="{{ route('produtos.index') }}"
           class="nav-link {{ request()->routeIs('produtos.*') ? 'active' : '' }}">
          <i class="bi bi-cart icon"></i>
          <span class="label">Produtos</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ route('cupons.index') }}"
           class="nav-link {{ request()->routeIs('cupons.*') ? 'active' : '' }}">
          <i class="bi bi-ticket-perforated icon"></i>
          <span class="label">Cupons</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ route('carrinho.index') }}"
           class="nav-link {{ request()->routeIs('carrinho.*') ? 'active' : '' }} d-flex align-items-center">
          <i class="bi bi-bag icon"></i>
          <span class="label">Carrinho</span>

          @if($cartCount > 0)
            <span class="badge rounded-pill bg-light text-secondary ms-auto">
              {{ $cartCount }}
            </span>
          @endif
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ route('pedidos.index') }}"
           class="nav-link {{ request()->routeIs('pedidos.*') ? 'active' : '' }}">
          <i class="bi bi-card-list icon"></i>
          <span class="label">Pedidos</span>
        </a>
      </li>
    </ul>
  </nav>

  {{-- CONTEÚDO --}}
  <div id="content">
    <div id="inner-header">
      <button id="btnToggleSidebar" title="Mostrar/Esconder menu">☰</button>
      <h1 class="h4 mb-0">@yield('title')</h1>
    </div>

    <main>
      @yield('content')
    </main>

    <footer class="text-center text-muted py-3 border-top">
      &copy; {{ date('Y') }} Mini ERP. Todos os direitos reservados. Enzo Patriarca.
    </footer>
  </div>

  {{-- SCRIPTS --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const body = document.body;
    const btn  = document.getElementById('btnToggleSidebar');

    document.addEventListener('DOMContentLoaded', () => {
      if (localStorage.getItem('sidebar-collapsed') === 'true') {
        body.classList.add('sidebar-collapsed');
      }
    });

    btn.addEventListener('click', () => {
      const collapsed = body.classList.toggle('sidebar-collapsed');
      localStorage.setItem('sidebar-collapsed', collapsed);
    });
  </script>
  @stack('scripts')
  @stack('modals')
</body>
</html>
