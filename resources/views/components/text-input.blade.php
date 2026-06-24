@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'block w-full rounded-xl border-gray-200 text-sm focus:border-[#386650] focus:ring-[#386650] bg-[#F4F3F0] text-gray-700 shadow-sm transition duration-150']) }}>
