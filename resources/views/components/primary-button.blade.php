<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 rounded-xl px-5 py-3 bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-bold text-sm shadow-lg shadow-indigo-600/25 hover:from-indigo-500 hover:to-violet-500 hover:shadow-xl active:scale-[.98] transition']) }}>
    {{ $slot }}
</button>
