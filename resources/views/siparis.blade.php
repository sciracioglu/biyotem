@extends('layouts.master')

@section('baslik')
    {{ session('musteri.unvan') }}
@endsection
@section('kucuk_baslik')
    Sipariş
@endsection

@section('icerik')
    <form @submit.prevent="kaydet" @keydown="form.errors.clear($event.target.name)">
        @include('ust_form')
        <div class="card border-warning"  v-if='satislar.length>0'>
            <div class="card-body">
                <table class='table table-condenced'>
                    <thead>
                        <tr>
                            <th>SeriNo</th>
                            <th>Mal Kod</th>
                            <th>Mal Ad</th>
                            <th>Fiyat</th>
                            <th>UBB</th>
                            <th>Lot No</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for='(satis,index) in satislar'>
                            <td>@{{satis.SERINO}}</td>
                            <td>@{{satis.MALKOD}}</td>
                            <td>@{{satis.MALAD}}</td>
                            <td>@{{satis.FIYAT}}</td>
                            <td>@{{satis.UBB}}</td>
                            <td>@{{satis.LOT}}</td>
                            <td><span class="text-danger" style="cursor:pointer;" @click='sil(index, satis.KALEMSN)'><i class="fa fa-trash"></i></span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    <div class='row'>
        <div class='col-12'>
            <div class="input-group mb-3">
                <input type="text" class="form-control" id='serino' @blur='bilgiAl' v-model='form.serino' placeholder="Seri No" aria-label="Seri No">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button" @click='bilgiAl'><i class="fa fa-search"></i></button>
                </div>
                <small id="serino" class="form-text text-danger" v-if="form.errors.has('serino')">Bu alan bos birakilamaz</small>
            </div>
        </div>
    </div>
    <div v-if='isLoading'><i class="fa fa-gear faa-spin animated fa-3x"></i></div>
    <div class="row" v-if='form.mal_kodu'>
        <div class='col-4 '>
            <div class="form-group">
                <div class="row">
                    <div class="col-3"><strong><label for="malkod">Mal Kodu</label></strong></div>
                    <div class="col-9">
                        <input type='text' class='form-control-plaintext' id='malkod' v-model='form.mal_kodu' readonly />
                    </div>
                </div>
            </div>
        </div>
        <div class='col-4'>
            <div class="form-group">
                <div class="row">
                    <div class="col-3"><strong><label for="malkod">Mal Ad</label></strong></div>
                    <div class="col-9"><input type='text' class='form-control-plaintext' id='malad' v-model='form.mal_adi' readonly /></div>
                </div>
            </div>
        </div>
        <div class='col-4'>
            <div class="form-group">
                <div class="row">
                    <div class="col-3"><strong><label for="fiyat">Fiyat</label></strong></div>
                    <div class="col-9"><input type='text' class='form-control-plaintext' id='fiyat' v-model='form.fiyat' readonly /></div>
                </div>
            </div>
        </div>
        <div class='col-4'>
            <div class="form-group">
                <div class="row">
                    <div class="col-3"><strong><label for="ubb">UBB Kodu</label></strong></div>
                    <div class="col-9"><input type='text' class='form-control-plaintext' id='ubb' v-model='form.ubb' readonly /></div>
                </div>
            </div>
        </div>
        <div class='col-4'>
            <div class="form-group">
                <div class="row">
                    <div class="col-3"><strong><label for="skt">S.K. Tarihi</label></strong></div>
                    <div class="col-9"><input type='text' class='form-control-plaintext' id='skt' v-model='form.skt' readonly /></div>
                </div>
            </div>
        </div>
        <div class='col-4'>
            <div class="form-group">
                <div class="row">
                    <div class="col-3"><strong><label for="lot">Lot No</label></strong></div>
                    <div class="col-9"><input type='text' class='form-control-plaintext' v-model='form.lot_no' id='form.lot' readonly /></div>
                </div>
            </div>
        </div>
    </div>
    <div class='row'>
        <div class='col'>
            <button type="submit" class="btn btn-sm btn-block btn-primary">Kaydet</button>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>

var vue=new Vue({
    el:'#app',
    data:{
        isLoading:false,
        satislar:[],
        satis_sekilleri:{!! $satis_sekilleri !!},
        hastaneler:{!! $hastahaneler !!},
        doktorlar:null,
        mal_kodu:null,
        mal_adi:null,
        fiyat:null,
        ubb:null,
        skt:null,
        lot_no:null,
        durum:null,
        evrak_no:'{{ $evrak_no }}',
        evrak_baslik:{!! session()->has('evrak_baslik') ? session('evrak_baslik') : "null" !!},
        form : new Form({
                satis_sekli:null,
                hastane:null,
                aciklama:null,
                doktor:null,
                hasta:null,
                kimlikno:null,
                protokol:null,
                serino:null,
                serino2:null,
                skt:null,
                ubb:null,
                tarihi:null,
                depokod:null,
                evrak_no:'{{ $evrak_no }}',
            }),
    },
    methods:{
        kaydet(){ 
            self = this;
            isLoading=true;
            this.form.post('/siparis')
                .then(function(){
                    self.form.serino=null;
                    self.form.serino2=null;
                    self.form.ubb=null;
                    self.form.skt = null;
                    self.form.depokod = null;
                    self.form.mal_kodu = null;
                    self.form.mal_adi = null;
                    self.liste();
                    self.isLoading=false;
                })
        },
        liste(){
            this.isLoading=true;
            self=this;
            axios.get('/siparis/'+this.evrak_no)
                    .then(function(response){
                        self.satislar = response.data;
                        self.isLoading=false;
                    });
        },
        bilgiAl(){
            if(this.form.serino){
                self = this;
                this.isLoading=true;
                axios.get('/siparis?serino='+this.form.serino)
                    .then(function(response){
                        if(response.data[0].DURUM==1){
                            self.form.fiyat = response.data[0].FIYAT;
                            self.form.ubb = response.data[0].BARKOD1;
                            self.form.skt = response.data[0].SKT;
                            self.form.serino2 = response.data[0].LOTNO;
                            self.form.mal_kodu = response.data[0].MALKOD;
                            self.form.mal_adi = response.data[0].MALAD;
                            self.form.tarihi = response.data[0].TARIHI;
                            self.form.depokod = response.data[0].DEPOKOD;
                        } else if(response.data[0].DURUM==0){
                            self.form.serino = null;
                            alert('Seri no bulunamadı!');
                        } else if(response.data[0].DURUM==2){
                            self.form.serino = null;
                            alert('Seri daha önce kullanılmış!');
                        }
                        self.isLoading=false;
                    });
            }
        },
        sil(index,kalem){
            self = this;
            var sor=confirm('Silmek istediginize emin misiniz?')
            if(sor){
                this.isLoading=true;
                axios.delete('/siparis/'+kalem)
                    .then(function(){
                        self.liste();
                    });
            }
        },
    },
})

</script>
@endsection