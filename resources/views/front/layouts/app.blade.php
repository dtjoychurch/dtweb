<!DOCTYPE html>
<html lang="en">
  @include('front.layouts.header')
  <body>
    <div class="wrapper d-flex flex-column min-vh-100">
      <header class="position-fixed top-0 start-0 w-100 z-3">
        @include('front.layouts.nav')
      </header>

      <main class="flex-grow-1">
        @if ($errors->any())
          <div class="container page-content pb-0">
            <div class="alert alert-danger">
              <ul class="mb-0">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          </div>
        @endif

        @yield('content')
      </main>

      @include('front.layouts.footer')
    </div>
  </body>
</html>