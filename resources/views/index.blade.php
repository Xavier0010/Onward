<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('OnwardLogo.png') }}">

    <title>Onward — Level Up Your Productivity</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom styles */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        
        * {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }
        
        body {
            background-color: #0f1117;
            color: #e2e8f0;
        }
        
        .glass-nav {
            background-color: rgba(15, 17, 23, 0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(42, 45, 62, 0.5);
        }
        
        .glow-green {
            box-shadow: 0 0 80px rgba(34, 197, 94, 0.15);
        }
        
        .smooth-scroll {
            scroll-behavior: smooth;
        }
        
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }
        
        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body class="smooth-scroll">
    <!-- Navbar -->
    <nav class="glass-nav fixed top-0 left-0 right-0 z-50 px-8 py-4">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <!-- Logo -->
            <a href="#" class="text-2xl font-extrabold text-[#22c55e]">
                <img src="{{ asset('OnwardLogo.png') }}" alt="" class="w-20 h-20">
            </a>
            
            <!-- Navigation Links -->
            <div class="flex items-center gap-8">
                <a href="#features" class="text-sm font-medium text-gray-400 hover:text-white transition-colors">Features</a>
                <a href="#friends" class="text-sm font-medium text-gray-400 hover:text-white transition-colors">Friends</a>
                <a href="#rewards" class="text-sm font-medium text-gray-400 hover:text-white transition-colors">Rewards</a>
                <a href="#stats" class="text-sm font-medium text-gray-400 hover:text-white transition-colors">Stats</a>
                
                @auth
                    <a href="{{ route('user.dashboard') }}" class="px-5 py-2 bg-[#22c55e] text-white font-semibold rounded-lg hover:bg-[#1ea951] transition-colors">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="px-5 py-2 border-2 border-[#22c55e] text-[#22c55e] font-semibold rounded-lg bg-transparent hover:bg-[#22c55e]/10 transition-colors">
                        Login
                    </a>
                        
                    <a href="{{ route('register') }}"
                       class="px-5 py-2 bg-[#22c55e] text-white font-semibold rounded-lg hover:bg-[#1ea951] transition-colors">
                        Sign Up
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="hero" class="min-h-screen flex items-center pt-24 pb-16 px-8">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div class="space-y-8">
                <h1 class="text-6xl lg:text-7xl font-extrabold leading-tight text-white">
                    Level Up Your Productivity.
                </h1>
                <p class="text-xl text-gray-400 leading-relaxed">
                    Turn everyday tasks into achievements. Build streaks, compete with friends, unlock rewards, and track your growth.
                </p>
                <div class="flex gap-4">
                    <a href="{{ route('register') }}" class="px-8 py-4 bg-[#22c55e] text-white font-bold text-lg rounded-xl hover:bg-[#1ea951] transition-all hover:scale-105">
                        Start with Onward
                    </a>
                    <a href="#features" class="px-8 py-4 bg-[#1a1d27] text-white font-semibold text-lg rounded-xl border border-[#2a2d3e] hover:bg-[#1f2235] transition-colors">
                        Explore Features
                    </a>
                </div>
            </div>
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-br from-[#22c55e]/20 to-transparent rounded-3xl blur-2xl -z-10"></div>
                <div class="bg-[#1a1d27] border border-[#2a2d3e] rounded-3xl p-8 glow-green">
                    <div class="space-y-6">
                        <!-- Mockup Header -->
                        <div class="flex items-center justify-between">
                            <div class="w-24 h-6 bg-[#2a2d3e] rounded"></div>
                            <div class="flex gap-2">
                                <div class="w-3 h-3 bg-gray-600 rounded-full"></div>
                                <div class="w-3 h-3 bg-gray-600 rounded-full"></div>
                                <div class="w-3 h-3 bg-gray-600 rounded-full"></div>
                            </div>
                        </div>
                        
                        <!-- Mockup Content -->
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Task List -->
                            <div class="space-y-3">
                                <div class="w-20 h-4 bg-[#22c55e]/30 rounded"></div>
                                <div class="space-y-2">
                                    <div class="flex items-center gap-3 bg-[#0f1117] p-3 rounded-lg">
                                        <div class="w-5 h-5 rounded-full border-2 border-[#22c55e]"></div>
                                        <div class="w-32 h-3 bg-[#2a2d3e] rounded"></div>
                                    </div>
                                    <div class="flex items-center gap-3 bg-[#0f1117] p-3 rounded-lg">
                                        <div class="w-5 h-5 rounded-full bg-[#22c55e]"></div>
                                        <div class="w-28 h-3 bg-[#2a2d3e] rounded"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Achievements -->
                            <div class="space-y-3">
                                <div class="w-24 h-4 bg-[#22c55e]/30 rounded"></div>
                                <div class="grid grid-cols-3 gap-2">
                                    <div class="aspect-square bg-[#0f1117] rounded-lg flex items-center justify-center text-2xl">🏆</div>
                                    <div class="aspect-square bg-[#0f1117] rounded-lg flex items-center justify-center text-2xl">🔥</div>
                                    <div class="aspect-square bg-[#0f1117] rounded-lg flex items-center justify-center text-2xl">⭐</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Streak & Leaderboard -->
                        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-[#2a2d3e]">
                            <div class="bg-[#0f1117] rounded-xl p-4">
                                <div class="w-16 h-3 bg-gray-600 rounded mb-3"></div>
                                <div class="text-4xl font-bold text-[#f59e0b]">14</div>
                                <div class="w-12 h-3 bg-[#2a2d3e] rounded mt-1"></div>
                            </div>
                            <div class="bg-[#0f1117] rounded-xl p-4">
                                <div class="w-20 h-3 bg-gray-600 rounded mb-3"></div>
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-5 h-5 bg-yellow-500 rounded-full"></div>
                                        <div class="w-16 h-3 bg-[#2a2d3e] rounded"></div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-5 h-5 bg-gray-400 rounded-full"></div>
                                        <div class="w-14 h-3 bg-[#2a2d3e] rounded"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 1: Gamified To-Do List -->
    <section id="features" class="py-32 px-8">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-20 items-center fade-in">
            <div class="relative">
                <div class="bg-[#1a1d27] border border-[#2a2d3e] rounded-3xl p-8">
                    <div class="space-y-4">
                        <div class="w-24 h-5 bg-[#22c55e]/30 rounded"></div>
                        <div class="space-y-3">
                            <div class="flex items-center gap-4">
                                <div class="w-6 h-6 rounded-full border-2 border-[#22c55e]"></div>
                                <div class="flex-1 h-4 bg-[#2a2d3e] rounded"></div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="w-6 h-6 rounded-full bg-[#22c55e]"></div>
                                <div class="flex-1 h-4 bg-[#2a2d3e] rounded"></div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="w-6 h-6 rounded-full border-2 border-[#2a2d3e]"></div>
                                <div class="flex-1 h-4 bg-[#2a2d3e] rounded"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="space-y-6">
                <div class="text-[#22c55e] font-bold text-sm tracking-widest uppercase">PRODUCTIVITY</div>
                <h2 class="text-5xl font-bold text-white leading-tight">
                    Tasks that feel rewarding.
                </h2>
                <p class="text-lg text-gray-400 leading-relaxed">
                    Transform ordinary tasks into meaningful progress. Complete objectives, gain experience points, maintain streaks, and stay motivated every day.
                </p>
                <div class="grid grid-cols-2 gap-4 pt-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-[#22c55e]/10 rounded-lg flex items-center justify-center text-[#22c55e]">
                            ⚡
                        </div>
                        <span class="text-gray-300 font-medium">Gain XP</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-[#22c55e]/10 rounded-lg flex items-center justify-center text-[#22c55e]">
                            🔥
                        </div>
                        <span class="text-gray-300 font-medium">Build streaks</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-[#22c55e]/10 rounded-lg flex items-center justify-center text-[#22c55e]">
                            📋
                        </div>
                        <span class="text-gray-300 font-medium">Complete daily goals</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-[#22c55e]/10 rounded-lg flex items-center justify-center text-[#22c55e]">
                            🎯
                        </div>
                        <span class="text-gray-300 font-medium">Stay consistent</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 2: Compete With Friends -->
    <section id="friends" class="py-32 px-8 bg-gradient-to-b from-transparent to-[#1a1d27]/30">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-20 items-center fade-in">
            <div class="space-y-6">
                <div class="text-[#22c55e] font-bold text-sm tracking-widest uppercase">SOCIAL</div>
                <h2 class="text-5xl font-bold text-white leading-tight">
                    Stay accountable together.
                </h2>
                <p class="text-lg text-gray-400 leading-relaxed">
                    Add friends, compare progress, and challenge each other to stay productive. Turn self-improvement into a shared experience.
                </p>
                <div class="grid grid-cols-2 gap-4 pt-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-[#22c55e]/10 rounded-lg flex items-center justify-center text-[#22c55e]">
                            📊
                        </div>
                        <span class="text-gray-300 font-medium">Friend leaderboard</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-[#22c55e]/10 rounded-lg flex items-center justify-center text-[#22c55e]">
                            📅
                        </div>
                        <span class="text-gray-300 font-medium">Weekly rankings</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-[#22c55e]/10 rounded-lg flex items-center justify-center text-[#22c55e]">
                            📈
                        </div>
                        <span class="text-gray-300 font-medium">Progress comparison</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-[#22c55e]/10 rounded-lg flex items-center justify-center text-[#22c55e]">
                            🤝
                        </div>
                        <span class="text-gray-300 font-medium">Friendly competition</span>
                    </div>
                </div>
            </div>
            
            <div class="relative">
                <div class="bg-[#1a1d27] border border-[#2a2d3e] rounded-3xl p-8">
                    <div class="space-y-4">
                        <div class="w-32 h-5 bg-[#22c55e]/30 rounded"></div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between bg-[#0f1117] p-4 rounded-xl border border-yellow-500/30">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full bg-yellow-500/20 text-yellow-500 flex items-center justify-center font-bold">🥇</div>
                                    <div class="w-24 h-4 bg-[#2a2d3e] rounded"></div>
                                </div>
                                <div class="w-16 h-4 bg-yellow-500/30 rounded"></div>
                            </div>
                            <div class="flex items-center justify-between bg-[#0f1117] p-4 rounded-xl border border-gray-400/30">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full bg-gray-400/20 text-gray-400 flex items-center justify-center font-bold">🥈</div>
                                    <div class="w-20 h-4 bg-[#2a2d3e] rounded"></div>
                                </div>
                                <div class="w-14 h-4 bg-gray-400/30 rounded"></div>
                            </div>
                            <div class="flex items-center justify-between bg-[#0f1117] p-4 rounded-xl border border-amber-700/30">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full bg-amber-700/20 text-amber-700 flex items-center justify-center font-bold">🥉</div>
                                    <div class="w-22 h-4 bg-[#2a2d3e] rounded"></div>
                                </div>
                                <div class="w-12 h-4 bg-amber-700/30 rounded"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 3: Earn Rewards -->
    <section id="rewards" class="py-32 px-8">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-20 items-center fade-in">
            <div class="relative">
                <div class="bg-[#1a1d27] border border-[#2a2d3e] rounded-3xl p-8">
                    <div class="space-y-4">
                        <div class="w-28 h-5 bg-[#22c55e]/30 rounded"></div>
                        <div class="grid grid-cols-4 gap-3">
                            <div class="aspect-square bg-[#0f1117] rounded-xl flex items-center justify-center text-3xl">🏆</div>
                            <div class="aspect-square bg-[#0f1117] rounded-xl flex items-center justify-center text-3xl">🔥</div>
                            <div class="aspect-square bg-[#0f1117] rounded-xl flex items-center justify-center text-3xl">⭐</div>
                            <div class="aspect-square bg-[#0f1117] rounded-xl flex items-center justify-center text-3xl">🎖️</div>
                            <div class="aspect-square bg-[#0f1117] rounded-xl flex items-center justify-center text-3xl">🏅</div>
                            <div class="aspect-square bg-[#0f1117] rounded-xl flex items-center justify-center text-3xl">🎯</div>
                            <div class="aspect-square bg-[#0f1117] rounded-xl flex items-center justify-center text-3xl">📈</div>
                            <div class="aspect-square bg-[#0f1117] rounded-xl flex items-center justify-center text-3xl">🌟</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="space-y-6">
                <div class="text-[#22c55e] font-bold text-sm tracking-widest uppercase">ACHIEVEMENTS</div>
                <h2 class="text-5xl font-bold text-white leading-tight">
                    Every milestone deserves recognition.
                </h2>
                <p class="text-lg text-gray-400 leading-relaxed">
                    Unlock badges, complete challenges, and build a collection that reflects your dedication and consistency.
                </p>
                <div class="grid grid-cols-2 gap-4 pt-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-[#22c55e]/10 rounded-lg flex items-center justify-center text-[#22c55e]">
                            🏆
                        </div>
                        <span class="text-gray-300 font-medium">Achievement system</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-[#22c55e]/10 rounded-lg flex items-center justify-center text-[#22c55e]">
                            💎
                        </div>
                        <span class="text-gray-300 font-medium">Rare badges</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-[#22c55e]/10 rounded-lg flex items-center justify-center text-[#22c55e]">
                            🎊
                        </div>
                        <span class="text-gray-300 font-medium">Milestone rewards</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-[#22c55e]/10 rounded-lg flex items-center justify-center text-[#22c55e]">
                            📚
                        </div>
                        <span class="text-gray-300 font-medium">Progress collection</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 4: Flex Your Stats -->
    <section id="stats" class="py-32 px-8 bg-gradient-to-b from-transparent to-[#1a1d27]/30">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-20 items-center fade-in">
            <div class="space-y-6">
                <div class="text-[#22c55e] font-bold text-sm tracking-widest uppercase">ANALYTICS</div>
                <h2 class="text-5xl font-bold text-white leading-tight">
                    See how far you've come.
                </h2>
                <p class="text-lg text-gray-400 leading-relaxed">
                    Track streaks, weekly XP, completed tasks, rankings, and achievement progress in one beautiful profile.
                </p>
                <div class="grid grid-cols-2 gap-4 pt-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-[#22c55e]/10 rounded-lg flex items-center justify-center text-[#22c55e]">
                            🔥
                        </div>
                        <span class="text-gray-300 font-medium">Streak tracking</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-[#22c55e]/10 rounded-lg flex items-center justify-center text-[#22c55e]">
                            ⚡
                        </div>
                        <span class="text-gray-300 font-medium">Weekly XP</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-[#22c55e]/10 rounded-lg flex items-center justify-center text-[#22c55e]">
                            ✅
                        </div>
                        <span class="text-gray-300 font-medium">Task completion history</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-[#22c55e]/10 rounded-lg flex items-center justify-center text-[#22c55e]">
                            🏆
                        </div>
                        <span class="text-gray-300 font-medium">Achievement showcase</span>
                    </div>
                </div>
            </div>
            
            <div class="relative">
                <div class="bg-[#1a1d27] border border-[#2a2d3e] rounded-3xl p-8">
                    <div class="space-y-6">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-full bg-[#2a2d3e]"></div>
                            <div class="space-y-2">
                                <div class="w-32 h-4 bg-[#e2e8f0] rounded"></div>
                                <div class="w-24 h-3 bg-[#2a2d3e] rounded"></div>
                            </div>
                        </div>
                        <div class="grid grid-cols-4 gap-4 pt-4 border-t border-[#2a2d3e]">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-[#f59e0b]">14</div>
                                <div class="w-12 h-3 bg-[#2a2d3e] rounded mx-auto mt-1"></div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-[#22c55e]">340</div>
                                <div class="w-12 h-3 bg-[#2a2d3e] rounded mx-auto mt-1"></div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-white">87</div>
                                <div class="w-12 h-3 bg-[#2a2d3e] rounded mx-auto mt-1"></div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-400">#2</div>
                                <div class="w-12 h-3 bg-[#2a2d3e] rounded mx-auto mt-1"></div>
                            </div>
                        </div>
                        <div class="pt-4 border-t border-[#2a2d3e]">
                            <div class="w-36 h-4 bg-[#22c55e]/30 rounded mb-3"></div>
                            <div class="grid grid-cols-4 gap-2">
                                <div class="aspect-square bg-[#0f1117] rounded-lg flex items-center justify-center text-xl">🏆</div>
                                <div class="aspect-square bg-[#0f1117] rounded-lg flex items-center justify-center text-xl">🔥</div>
                                <div class="aspect-square bg-[#0f1117] rounded-lg flex items-center justify-center text-xl">⭐</div>
                                <div class="aspect-square bg-[#0f1117] rounded-lg flex items-center justify-center text-xl">🎖️</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA -->
    <section class="py-32 px-8">
        <div class="max-w-4xl mx-auto text-center fade-in">
            <div class="text-[#22c55e] font-bold text-sm tracking-widest uppercase mb-4">Ready to begin?</div>
            <h2 class="text-6xl font-extrabold text-white mb-6">
                Join Onward now!
            </h2>
            <p class="text-xl text-gray-400 mb-10 max-w-2xl mx-auto">
                Start building better habits today and turn progress into something worth celebrating.
            </p>
            <a href="{{ route('register') }}" class="inline-block px-12 py-5 bg-[#22c55e] text-white font-bold text-xl rounded-xl hover:bg-[#1ea951] transition-all hover:scale-105 shadow-lg shadow-[#22c55e]/20">
                Create Free Account
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-[#2a2d3e] py-12 px-8">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6 mb-8">
                <a href="#" class="text-2xl font-extrabold text-[#22c55e] h-24 w-24"><img src="{{ asset('OnwardLogo.png') }}" alt=""></a>
                <div class="flex gap-8">
                    <a href="#features" class="text-gray-400 hover:text-white transition-colors">Features</a>
                    <a href="#friends" class="text-gray-400 hover:text-white transition-colors">Friends</a>
                    <a href="#rewards" class="text-gray-400 hover:text-white transition-colors">Rewards</a>
                    <a href="#stats" class="text-gray-400 hover:text-white transition-colors">Stats</a>
                </div>
            </div>
            <div class="pt-8 border-t border-[#2a2d3e] text-center text-gray-600 text-sm">
                © 2026 Onward. All rights reserved. Built for productivity, powered by motivation.
            </div>
        </div>
    </footer>

    <script>
        // Fade-in animation on scroll
        document.addEventListener('DOMContentLoaded', function() {
            const fadeElements = document.querySelectorAll('.fade-in');
            
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };
            
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, observerOptions);
            
            fadeElements.forEach(el => {
                observer.observe(el);
            });
        });
    </script>
</body>
</html>
