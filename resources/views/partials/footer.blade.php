<!-- Begin Footer -->
<footer class="footer d-flex align-items-center text-center">
    <div class="container-fluid">
        @php
            $footerText = \App\Models\Setting::get(
                'footer_text',
                'Design & Developed by <a href="https://thesparkbiz.com/" target="_blank">❤️SparkBizTech</a>.',
            );
        @endphp
        <p class="mb-0">
            ©
            <script>
                document.write(new Date().getFullYear())
            </script>
            {!! $footerText !!}
        </p>
    </div>
</footer>
<!-- END Footer -->
