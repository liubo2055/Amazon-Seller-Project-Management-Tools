@php
  $url=user()->image_url?:EMPTY_IMAGE;
@endphp
<nav class="navbar-default navbar-static-side">
  <div class="sidebar-collapse">
    <ul
      class="nav metismenu"
      id="side-menu"
    >
      <li class="nav-header">
        <div class="profile-element">
          <a href="{{ route('dashboard') }}">
            <img
              class="img-circle"
              src="{{ $url }}"
            >
          </a>
        </div>
        <div class="dropdown profile-element">
          <a
            data-toggle="dropdown"
            class="dropdown-toggle"
            href="javascript:"
          >
            <span class="clear">
              <span class="block m-t-xs">
                <strong class="font-bold">{{ user()->name }}</strong>
              </span>
              <span class="text-muted text-xs block">
                {{ user()->roleName() }}
                <b class="caret"></b>
              </span>
            </span>
          </a>
          <ul class="dropdown-menu animated fadeInRight m-t-xs">
            <li>
              <a href="{{ route('profile') }}">{{ _ix('Profile','Menu option') }}</a>
            </li>
            <li class="divider"></li>
            <li>
              <a
                href="javascript:"
                onclick="logout()"
              >
                {{ _ix('Logout','Menu option') }}
              </a>
            </li>
          </ul>
        </div>
        <div class="logo-element">
          <img src="{{ asset('img/logo.png') }}">
        </div>
      </li>
      @inject('menuManager','Hui\Xproject\Services\MenuManager\MenuManager')
      @foreach($menuManager->options() as $option)
        <li class="{{ $option->isActive()?'active':'' }}">
          @if(!$option->options)
            <a
              href="{{ $option->url }}"
              target="{{ $option->newTab?'_blank':'' }}"
            >
              <i class="fa fa-{{ $option->icon }}"></i>
              <span class="nav-label">
                {{ $option->title }}
                @if($option->badge)
                  <span class="badge badge-info">{{ $option->badge }}</span>
                @endif
              </span>
            </a>
          @else
            <a href="javascript:">
              <i class="fa fa-{{ $option->icon }}"></i>
              <span class="nav-label">
                {{ $option->title }}
                @if($option->badge)
                  <span class="badge badge-info">{{ $option->badge }}</span>
                @endif
              </span>
              <span class="fa arrow"></span>
            </a>
            <ul class="nav nav-second-level collapse">
              @foreach($option->options as $subOption)
                <li class="{{ $subOption->isActive()?'active':'' }}">
                  <a href="{{ $subOption->url }}">
                    {{ $subOption->title }}
                    @if($subOption->badge)
                      <span class="badge badge-info">{{ $subOption->badge }}</span>
                    @endif
                  </a>
                </li>
              @endforeach
            </ul>
          @endif
        </li>
      @endforeach
    </ul>
  </div>
</nav>
