@extends('layouts.user')
@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.css">
@endsection
@section('body')
{{-- <script>
       document.documentElement.style.overflow = 'hidden';
</script> --}}
<section x-data="{show:false}" class="pt-20">
    <div x-show="show"
        class="fixed flex top-0 z-50 justify-center items-center bg-inr-black bg-opacity-50 h-screen w-full overflow-hidden">
        <div class="w-full m-10 md:w-8/12 lg:w-4/12 bg-inr-white rounded-lg">
            <div class="p-4 border-b border-slate-300 flex justify-between">
                <span>Sesuaikan Gambar</span>
                <button id="close_crop" @click="show=false"><i class="fas fa-xmark"></i></button>
            </div>
            <div class="p-4">
                <img src="" alt="" id="cropping" class="croppie">
            </div>
            <div class="p-4 border-t border-slate-300 flex justify-end">
                <div class="flex gap-3 text-sm font-light">
                    <button @click="show=false" class="rounded bg-gray-300 px-3 py-1">Batal</button>
                    <button id="crop_image" class="rounded bg-inr-yellow px-3 py-1">Terapkan</button>
                </div>
            </div>
        </div>
    </div>
    <div class="wrapper py-20">
        <div class="flex">
            @include('partials.user.setting_sidebar')
            <div class="grow">
                <x-forms.profile-setting title="Profil Pengguna" route="{{ 'hg' }}">
                    <div class="group mb-7">
                        <span class="font-semibold block mb-2">Foto Diri</span>
                        <input type="file" x-ref="image" name="image" id="image" class="hidden" x-on:change="show=true"
                            onchange="readImg(this)">
                        <div class="flex gap-5">
                            <img id="profile_image" src="{{ asset('images/ui/eren.jpeg') }}"
                                alt="" class="w-28 h-28 object-cover rounded">
                            <div>
                                <button @click="$refs.image.click()" type="button" class="btn-yellow text-sm">Pilih
                                    Foto</button>
                                <p class="font-extralight mt-4 text-sm max-w-md">Gambar Profile Anda sebaiknya memiliki
                                    rasio 1:1
                                    dan berukuran tidak lebih dari 2MB.</p>
                            </div>
                        </div>
                    </div>
                    <label for="" class="mb-7 block">
                        <span class="font-semibold block mb-2">Nama Lengkap <span class="text-rose-500">*</span></span>
                        <input type="text" class="form-control w-9/12">
                    </label>
                    <label for="" class="mb-7 block">
                        <span class="font-semibold block mb-2">Username <span class="text-rose-500">*</span></span>
                        <input type="text" class="form-control w-9/12">
                    </label>
                    <label for="" class="mb-7 block">
                        <span class="font-semibold block mb-2">Headline</span>
                        <input type="text" class="form-control w-9/12"
                            placeholder="Contoh : Software Enginer at Inready Workgroup">
                    </label>
                    <label for="" class="mb-7 block">
                        <span class="font-semibold block mb-2">Tentang Saya</span>
                        <textarea name="" id="" rows="3" class="form-control w-9/12 "
                            placeholder="Tulis sesuatu tentang anda"></textarea>
                    </label>
                </x-forms.profile-setting>
            </div>
        </div>
    </div>

</section>
@endsection
@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.3.min.js"
    integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.js"></script>
<script>
    const preview = $('.croppie').croppie({
        viewport: {
            width: 250,
            height: 250,
            type: 'square'
        },
        boundary: {
            width: 300,
            height: 300
        },
    });

    $('#crop_image').click(() => {
        preview.croppie('result', {
            type: 'base64',
            format: 'png'
        }).then((image) => {
            $('#profile_image').attr('src', image);
            $('#image').val(image);
        })
        $('#close_crop').trigger('click');
    })

    function readImg(e) {
        const file = e.files[0];
        if (file) {
            let reader = new FileReader();
            reader.onload = function (event) {
                preview.croppie('bind', {
                    url: event.target.result
                });
            }
            reader.readAsDataURL(file);
        }
    }

</script>
@endsection
