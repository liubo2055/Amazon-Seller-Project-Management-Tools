<div class="row border-bottom">
  <nav class="navbar navbar-static-top white-bg">
    <div class="navbar-header">
      <a
        class="navbar-minimalize minimalize-styl-2 btn btn-primary"
        href="#"
      >
        <i class="fa fa-bars"></i>
      </a>
    </div>
    <ul class="nav navbar-top-links navbar-right">
      @if(!user()->isComplete())
        <li>
          <span class="m-r-sm text-danger">{{ _ix('You must complete you profile in order to get access to the application.','Top nav bar') }}</span>
        </li>
      @endif
      <li>
        <span class="m-r-sm text-muted welcome-message">{{ _ix('Welcome to X-project','Top nav bar') }}</span>
      </li>
      <li>
        <div>
          <a
            class="btn btn-link"
            href="{{ route('profile') }}"
          >
            <i class="fa fa-user"></i>
            {{ _ix('Profile','Top nav bar') }}
          </a>
        </div>
      </li>
      <li>
        <form
          action="{{ route('logout') }}"
          class="form-horizontal form-logout"
          id="formLogout"
          method="POST"
        >
          {{ csrf_field() }}
          <button
            class="btn btn-link"
            type="submit"
          >
            <i class="fa fa-sign-out"></i>
            {{ _ix('Log out','Top nav bar') }}
          </button>
        </form>
      </li>
      <li>
        <form
          action="{{ route('locale') }}"
          class="form-horizontal"
          method="POST"
        >
          {{ csrf_field() }}
          <input
            name="url"
            type="hidden"
            value="{{ Request::url() }}"
          >
          <button
            class="btn btn-link"
            name="locale"
            type="submit"
            value="{{ LOCALE_EN }}"
          >
            <span class="flag-icon flag-icon-us"></span>
          </button>
          <button
            class="btn btn-link"
            name="locale"
            type="submit"
            value="{{ LOCALE_CN }}"
          >
            <span class="flag-icon flag-icon-cn"></span>
          </button>
        </form>
      </li>
    </ul>
  </nav>
</div>
