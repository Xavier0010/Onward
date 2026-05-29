<div class="bg-[#2a2a2a] w-full max-w-[500px] rounded-2xl p-10 shadow-lg mx-4">
    <div class="text-center mb-8">
        <h2 class="text-white text-3xl font-bold mb-2">Welcome to Onward</h2>
        <p class="text-gray-300 text-lg">Create a new account</p>
    </div>

    <form wire:submit.prevent="signup" class="flex flex-col gap-4">
        
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-[10px] text-gray-400 mb-1 font-medium uppercase tracking-wider">First Name</label>
                <input wire:model="first_name" type="text" 
                    class="w-full bg-transparent border border-gray-500 text-white rounded-lg py-3 px-4 focus:outline-none focus:border-green-500 transition-colors">
                @error('first_name') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-[10px] text-gray-400 mb-1 font-medium uppercase tracking-wider">Last Name</label>
                <input wire:model="last_name" type="text" 
                    class="w-full bg-transparent border border-gray-500 text-white rounded-lg py-3 px-4 focus:outline-none focus:border-green-500 transition-colors">
                @error('last_name') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

        <div>
            <label class="block text-[10px] text-gray-400 mb-1 font-medium uppercase tracking-wider">Username</label>
            <input wire:model="username" type="text" 
                class="w-full bg-transparent border border-gray-500 text-white rounded-lg py-3 px-4 focus:outline-none focus:border-green-500 transition-colors">
            @error('username') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-[10px] text-gray-400 mb-1 font-medium uppercase tracking-wider">Email</label>
            <input wire:model="email" type="email" 
                class="w-full bg-transparent border border-gray-500 text-white rounded-lg py-3 px-4 focus:outline-none focus:border-green-500 transition-colors">
            @error('email') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-[10px] text-gray-400 mb-1 font-medium uppercase tracking-wider">Gender</label>
                <div class="relative">
                    <select wire:model="sex" 
                        class="w-full bg-[#2a2a2a] border border-gray-500 text-white rounded-lg py-3 pl-4 pr-8 focus:outline-none focus:border-green-500 transition-colors appearance-none cursor-pointer">
                        <option value="" disabled hidden></option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                    </div>
                </div>
                @error('sex') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-[10px] text-gray-400 mb-1 font-medium uppercase tracking-wider">Date of Birth</label>
                <input wire:model="date_of_birth" type="date" 
                    class="w-full bg-transparent border border-gray-500 text-white rounded-lg py-3 pl-4 pr-3 focus:outline-none focus:border-green-500 transition-colors"
                    style="color-scheme: dark;">
                @error('date_of_birth') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

        <div>
            <label class="block text-[10px] text-gray-400 mb-1 font-medium uppercase tracking-wider">Password</label>
            <input wire:model="password" type="password" 
                class="w-full bg-transparent border border-gray-500 text-white rounded-lg py-3 px-4 focus:outline-none focus:border-green-500 transition-colors">
            @error('password') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-[10px] text-gray-400 mb-1 font-medium uppercase tracking-wider">Confirm Password</label>
            <input wire:model="password_confirmation" type="password" 
                class="w-full bg-transparent border border-gray-500 text-white rounded-lg py-3 px-4 focus:outline-none focus:border-green-500 transition-colors">
        </div>

        <button type="submit" 
            class="w-full mt-4 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg py-3 px-4 transition-colors cursor-pointer">
            Register
        </button>
    </form>

    <div class="mt-8 text-sm text-center text-gray-300">
        <a href="/login" class="text-green-500 hover:text-green-600 font-medium transition-colors">Login</a> if you already have an account
    </div>
</div>
