<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>SwiftBites</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-b from-green-200 to-gray-300 text-gray-900">
    <header class="bg-gray-800 shadow">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <ul class="flex items-center space-x-4">
                <h1 class="text-white text-xl font-bold mr-10">SwiftBites</h1>
                <li><a href="index.php" class="text-white text-l underline">Home</a></li>
            </ul>
        </div>
    </header>

    <main class="max-w-5xl mx-auto p-6 space-y-12">

        <section class="flex flex-col md:flex-row items-center gap-8">
            <img src="Team.jpeg" alt="Team photo" class="w-48 h-48 rounded-full object-cover shadow-md" />
            <div>
                <h2 class="text-2xl font-semibold mb-2">Who We Are</h2>
                <p class="text-gray-700">
                    We are a passionate team of developers and designers committed to building streamlined digital solutions.
                    Our focus is on efficiency, usability, and innovation. We believe in the power of technology to transform
                    everyday experiences into something extraordinary.
                </p>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-semibold mb-4">Our Mission</h2>
            <p class="text-gray-700 leading-relaxed">
                Our mission is to empower institutions through intelligent, accessible, and robust software systems.
                We aim to solve real-world problems with scalable solutions that are easy to use and maintain. Every
                project we build reflects our dedication to quality and long-term value.
            </p>
        </section>

        <section class="bg-white rounded-xl p-6 shadow-md">
            <h2 class="text-xl font-semibold mb-2">Want to Work With Us?</h2>
            <p class="text-gray-700 mb-4">We’re always looking to collaborate with like-minded individuals or institutions. Reach out and let’s talk.</p>
            <a href="contact.php" class="inline-block bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 transition">
                Contact Us
            </a>
        </section>

    </main>

    <footer class="bg-gray-800 border-t mt-10">
        <div class="max-w-7xl mx-auto px-4 py-6 text-center text-sm text-gray-400">
            © <?= date('Y') ?> SwiftBites. All rights reserved.
        </div>
    </footer>
</body>

</html>