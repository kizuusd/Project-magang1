<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-5 py-2.5 bg-[#386650] border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#2d5241] focus:outline-none focus:ring-2 focus:ring-[#386650]/40 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm']) }}>
    {{ $slot }}
</button>
