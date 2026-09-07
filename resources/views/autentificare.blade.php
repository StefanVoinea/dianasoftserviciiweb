<!doctype html>
{{--
  [2026-09-01] Pagina de autentificare in browser, folosita de pasul de autorizare
  OAuth. Deliberat autonoma: nu extinde niciun layout si nu incarca niciun asset,
  ca sa nu depinda de build-ul de frontend si sa poata fi citita dintr-o privire.
--}}
<html lang="ro">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Autentificare — DianaSoft</title>
  <style>
    :root {
      --ground: #eef1f4; --card: #ffffff; --ink: #16222c; --ink-mid: #5b6b78;
      --line: #d3dbe2; --accent: #0a4673; --accent-hover: #0d5a91; --stop: #a32b20;
      --stop-bg: #fbe6e3;
    }
    @media (prefers-color-scheme: dark) {
      :root {
        --ground: #0d1519; --card: #16212a; --ink: #e2eaf0; --ink-mid: #9fb0bc;
        --line: #2b3a45; --accent: #5fb0d8; --accent-hover: #7cc3e6; --stop: #f08b7e;
        --stop-bg: #3a1a16;
      }
    }
    * { box-sizing: border-box; }
    body {
      margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center;
      background: var(--ground); color: var(--ink); padding: 24px;
      font-family: "Segoe UI", system-ui, -apple-system, sans-serif;
    }
    .card {
      background: var(--card); border: 1px solid var(--line); border-radius: 10px;
      padding: 34px 32px; width: 100%; max-width: 400px;
      box-shadow: 0 1px 2px rgba(0,0,0,.06), 0 10px 30px rgba(0,0,0,.06);
    }
    h1 { font-size: 21px; margin: 0 0 6px; letter-spacing: -.01em; }
    .sub { color: var(--ink-mid); font-size: 14.5px; margin: 0 0 22px; line-height: 1.5; }
    label { display: block; font-size: 13px; color: var(--ink-mid); margin-bottom: 5px; }
    input[type=email], input[type=text], input[type=password] {
      width: 100%; padding: 10px 12px; font-size: 15px; margin-bottom: 16px;
      border: 1px solid var(--line); border-radius: 6px;
      background: var(--ground); color: var(--ink);
    }
    input:focus { outline: 2px solid var(--accent); outline-offset: 1px; border-color: transparent; }
    .rand-bifa { display: flex; align-items: center; gap: 8px; margin-bottom: 20px; font-size: 14px; color: var(--ink-mid); }
    button {
      width: 100%; padding: 11px; font-size: 15px; font-weight: 600; cursor: pointer;
      color: #fff; background: var(--accent); border: 0; border-radius: 6px;
    }
    button:hover { background: var(--accent-hover); }
    button:focus-visible { outline: 2px solid var(--ink); outline-offset: 2px; }
    .erori {
      background: var(--stop-bg); color: var(--stop); border-radius: 6px;
      padding: 10px 12px; font-size: 14px; margin-bottom: 18px;
    }
    .erori ul { margin: 0; padding-left: 18px; }
    .nota {
      margin-top: 22px; padding-top: 16px; border-top: 1px solid var(--line);
      font-size: 13px; color: var(--ink-mid); line-height: 1.5;
    }
  </style>
</head>
<body>
  <main class="card">
    <h1>DianaSoft</h1>
    @if ($motiv)
      <p class="sub">Autentificați-vă pentru a autoriza accesul cerut.</p>
    @else
      <p class="sub">Autentificare în aplicație.</p>
    @endif

    @if ($errors->any())
      <div class="erori">
        <ul>
          @foreach ($errors->all() as $eroare)
            <li>{{ $eroare }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('autentificare.intra') }}">
      @csrf

      <label for="email">E-mail</label>
      <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username">

      <label for="password">Parolă</label>
      <input id="password" name="password" type="password" required autocomplete="current-password">

      <div class="rand-bifa">
        <input id="tine_minte" name="tine_minte" type="checkbox" value="1">
        <label for="tine_minte" style="margin:0">Ține-mă minte</label>
      </div>

      <button type="submit">Intră</button>
    </form>

    @if ($motiv)
      <p class="nota">
        După autentificare veți vedea ce anume cere aplicația care solicită accesul
        și veți putea aproba sau refuza.
      </p>
    @endif
  </main>
</body>
</html>
