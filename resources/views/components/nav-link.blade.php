@props(['active' => false])

<li><a {{ $attributes }} class="{{ $active ? 'text-green-500 font-semibold' : 'text-gray-900' }} block py-2 px-3 md:p-0 text-gray-900 rounded hover:bg-gray-100 hover:font-semibold md:hover:bg-transparent md:hover:text-green-500 md:dark:hover:text-green-500 dark:text-white dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700 hover-underline-animation aria-current="{{ $active ? 'page' : false }} {{ request()->header('User-Agent') && preg_match('/Mobile|Android|iPhone/', request()->header('User-Agent')) ? 'block' : '' }}">{{ $slot }}</a></li>

