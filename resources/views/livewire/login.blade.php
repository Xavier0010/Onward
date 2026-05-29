<div class="bg-[#2a2a2a] w-full max-w-[500px] rounded-2xl p-10 shadow-lg mx-4">
    <div class="text-center mb-8">
        <h2 class="text-white text-3xl font-bold mb-2">Welcome back</h2>
        <p class="text-gray-300 text-lg">Please fill in your credentials</p>
    </div>

    <form wire:submit.prevent="login" class="flex flex-col gap-4">
        
        <div>
            <label class="block text-[10px] text-gray-400 mb-1 font-medium uppercase tracking-wider">Email or Username</label>
            <input wire:model="login_field" type="text" 
                class="w-full bg-transparent border border-gray-500 text-white rounded-lg py-3 px-4 focus:outline-none focus:border-green-500 transition-colors">
            @error('login_field') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-[10px] text-gray-400 mb-1 font-medium uppercase tracking-wider">Password</label>
            <input wire:model="password" type="password" 
                class="w-full bg-transparent border border-gray-500 text-white rounded-lg py-3 px-4 focus:outline-none focus:border-green-500 transition-colors">
            @error('password') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <button type="submit" 
            class="w-full mt-4 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg py-3 px-4 transition-colors cursor-pointer">
            Login
        </button>
    </form>

    <div class="mt-8 text-sm text-center text-gray-300">
        <a href="/register" class="text-green-500 hover:text-green-600 font-medium transition-colors">Register</a> if you don't have an account
    </div>
</div>
