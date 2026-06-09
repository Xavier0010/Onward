<div class="absolute inset-0 flex items-center justify-center overflow-hidden">

    <!-- Floating Background Icons -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">

        <span class="absolute top-[10%] left-[35%] text-6xl text-white/5 animate-float-slow">🏆</span>
        <span class="absolute top-[20%] right-[29%] text-6xl text-white/5 animate-float-medium">🔥</span>
        <span class="absolute bottom-[25%] left-[27%] text-6xl text-white/5 animate-float-fast">✅</span>
        <span class="absolute bottom-[12%] right-[36%] text-6xl text-white/5 animate-float-slow">📈</span>
        <span class="absolute top-[40%] left-[25%] text-6xl text-white/5 animate-float-medium">⭐</span>
        <span class="absolute top-[50%] right-[26%] text-6xl text-white/5 animate-float-fast">🎯</span>
        <span class="absolute top-[9%] left-[49%] text-6xl text-white/5 animate-float-fast">🥇</span>
        <span class="absolute top-[75%] right-[25%] text-6xl text-white/5 animate-float-slow">🥈</span>
        <span class="absolute bottom-[10%] left-[37%] text-6xl text-white/5 animate-float-medium">🥉</span>
    </div>

    <div class="relative z-10 bg-[#181c25] border border-[#232938] w-full max-w-[500px] rounded-2xl p-10 shadow-xl">
        <div class="text-center mb-8">
            <h2 class="text-white text-3xl font-bold mb-2">Welcome to Onward</h2>
            <p class="text-gray-300 text-lg">Create a new account</p>
        </div>

        <form wire:submit.prevent="signup" class="flex flex-col gap-4">

            @if($currentPage === 1)
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] text-gray-400 mb-1 font-medium uppercase tracking-wider">First Name</label>
                        <input wire:model="first_name" type="text" 
                            class="w-full bg-[#141923] border border-[#343b4f] text-white rounded-lg py-3 px-4 transition-all duration-200 focus:outline-none focus:border-[#22c55e] focus:ring-1 focus:ring-[#22c55e]" />
                        @error('first_name') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] text-gray-400 mb-1 font-medium uppercase tracking-wider">Last Name</label>
                        <input wire:model="last_name" type="text" 
                            class="w-full bg-[#141923] border border-[#343b4f] text-white rounded-lg py-3 px-4 transition-all duration-200 focus:outline-none focus:border-[#22c55e] focus:ring-1 focus:ring-[#22c55e]" />
                        @error('last_name') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] text-gray-400 mb-1 font-medium uppercase tracking-wider">Nationality</label>
                    <div class="relative">
                        <select wire:model="nationality" 
                            class="w-full bg-[#141923] border border-[#343b4f] text-white rounded-lg py-3 pl-4 pr-8 transition-all duration-200 focus:outline-none focus:border-[#22c55e] focus:ring-1 focus:ring-[#22c55e] appearance-none cursor-pointer">
                            <option value="" disabled hidden></option>
                            @foreach($countries as $country)
                                <option value="{{ $country }}">{{ $country }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                        </div>
                    </div>
                    @error('nationality') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] text-gray-400 mb-1 font-medium uppercase tracking-wider">Gender</label>
                        <div class="relative">
                            <select wire:model="gender" 
                                class="w-full bg-[#141923] border border-[#343b4f] text-white rounded-lg py-3 pl-4 pr-8 transition-all duration-200 focus:outline-none focus:border-[#22c55e] focus:ring-1 focus:ring-[#22c55e] appearance-none cursor-pointer">
                                <option value="" disabled hidden></option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                            </div>
                        </div>
                        @error('gender') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] text-gray-400 mb-1 font-medium uppercase tracking-wider">Date of Birth</label>
                        <input wire:model="date_of_birth" type="date" 
                            class="w-full bg-[#141923] border border-[#343b4f] text-white rounded-lg py-3 pl-4 pr-3 transition-all duration-200 focus:outline-none focus:border-[#22c55e] focus:ring-1 focus:ring-[#22c55e] cursor-text"
                            style="color-scheme: dark;" />
                        @error('date_of_birth') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <button type="button" wire:click="nextPage"
                    class="w-full mt-4 bg-[#22c55e] hover:bg-[#1ea951] text-white font-semibold rounded-lg py-3 px-4 transition-colors cursor-pointer">
                    Next
                </button>
            @else
                <div>
                    <label class="block text-[10px] text-gray-400 mb-1 font-medium uppercase tracking-wider">Username</label>
                    <input wire:model="username" type="text" 
                        class="w-full bg-[#141923] border border-[#343b4f] text-white rounded-lg py-3 px-4 transition-all duration-200 focus:outline-none focus:border-[#22c55e] focus:ring-1 focus:ring-[#22c55e]" />
                    @error('username') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-[10px] text-gray-400 mb-1 font-medium uppercase tracking-wider">Email</label>
                    <input wire:model="email" type="email" 
                        class="w-full bg-[#141923] border border-[#343b4f] text-white rounded-lg py-3 px-4 transition-all duration-200 focus:outline-none focus:border-[#22c55e] focus:ring-1 focus:ring-[#22c55e]" />
                    @error('email') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div x-data="{ show: false }">
                    <label class="block text-[10px] text-gray-400 mb-1 font-medium uppercase tracking-wider">Password</label>
                    <div class="relative">
                        <input wire:model="password" x-bind:type="show ? 'text' : 'password'" 
                            class="w-full bg-[#141923] border border-[#343b4f] text-white rounded-lg py-3 pl-4 pr-10 transition-all duration-200 focus:outline-none focus:border-[#22c55e] focus:ring-1 focus:ring-[#22c55e]" />
                        <button 
                            type="button" 
                            @click="show = !show" 
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-[#22c55e] transition-colors cursor-pointer"
                        >
                            <svg x-show="!show" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="show" style="display: none;" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                    @error('password') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div x-data="{ show: false }">
                    <label class="block text-[10px] text-gray-400 mb-1 font-medium uppercase tracking-wider">Confirm Password</label>
                    <div class="relative">
                        <input wire:model="password_confirmation" x-bind:type="show ? 'text' : 'password'" 
                            class="w-full bg-[#141923] border border-[#343b4f] text-white rounded-lg py-3 pl-4 pr-10 transition-all duration-200 focus:outline-none focus:border-[#22c55e] focus:ring-1 focus:ring-[#22c55e]" />
                        <button 
                            type="button" 
                            @click="show = !show" 
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-[#22c55e] transition-colors cursor-pointer"
                        >
                            <svg x-show="!show" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="show" style="display: none;" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex gap-4 mt-4">
                    <button type="button" wire:click="previousPage"
                        class="w-1/3 bg-[#1f2235] hover:bg-[#2a2d3e] text-white font-semibold border border-[#343b4f] rounded-lg py-3 px-4 transition-colors cursor-pointer">
                        Back
                    </button>
                    <button type="submit" 
                        class="w-2/3 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg py-3 px-4 transition-colors cursor-pointer">
                        Register
                    </button>
                </div>
            @endif
        </form>

        <div class="mt-8 text-sm text-center text-gray-300">
            <a href="/login" class="text-green-500 hover:text-green-600 font-medium transition-colors">Login</a> if you already have an account
        </div>
    </div>
</div>
<style>
    @keyframes floatSlow {
        0%,100% {
            transform: translateY(0px) rotate(0deg);
        }
        50% {
            transform: translateY(-20px) rotate(6deg);
        }
    }

    @keyframes floatMedium {
        0%,100% {
            transform: translateY(0px) rotate(0deg);
        }
        50% {
            transform: translateY(-30px) rotate(-8deg);
        }
    }

    @keyframes floatFast {
        0%,100% {
            transform: translateY(0px) rotate(0deg);
        }
        50% {
            transform: translateY(-40px) rotate(10deg);
        }
    }

    .animate-float-slow {
        animation: floatSlow 8s ease-in-out infinite;
    }

    .animate-float-medium {
        animation: floatMedium 6s ease-in-out infinite;
    }

    .animate-float-fast {
        animation: floatFast 4s ease-in-out infinite;
    }
</style>