# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: DiklatHLC\ProgramHLCSuccess.spec.ts >> Program HLC Kosong
- Location: tests\Playwright\DiklatHLC\ProgramHLCSuccess.spec.ts:3:1

# Error details

```
ReferenceError: namaProgram is not defined
```

# Page snapshot

```yaml
- generic [ref=e4]:
  - generic [ref=e7]:
    - list [ref=e9]:
      - listitem [ref=e10]:
        - link "Eichar" [ref=e11] [cursor=pointer]:
          - /url: /dashboard
          - generic [ref=e14]: Eichar
    - generic [ref=e16]:
      - generic [ref=e17]: Platform
      - list [ref=e18]:
        - listitem [ref=e19]:
          - link "Dashboard Diklat (admin)" [ref=e20] [cursor=pointer]:
            - /url: /dashboard
            - img [ref=e22]
            - generic [ref=e28]: Dashboard Diklat (admin)
        - listitem [ref=e29]:
          - link "Dashboard Diklat (user)" [ref=e30] [cursor=pointer]:
            - /url: http://localhost:8000/dashboard/user
            - img [ref=e32]
            - generic [ref=e38]: Dashboard Diklat (user)
        - listitem [ref=e39]:
          - link "Diklat" [ref=e40] [cursor=pointer]:
            - /url: /Diklat
            - img [ref=e42]
            - generic [ref=e46]: Diklat
        - listitem [ref=e47]:
          - link "Sertifikat Internal" [ref=e48] [cursor=pointer]:
            - /url: /DiklatInternal/user
            - img [ref=e50]
            - generic [ref=e56]: Sertifikat Internal
        - listitem [ref=e57]:
          - link "Persetujuan" [ref=e58] [cursor=pointer]:
            - /url: /Persetujuan
            - img [ref=e60]
            - generic [ref=e63]: Persetujuan
        - listitem [ref=e64]:
          - link "Library Materi" [ref=e65] [cursor=pointer]:
            - /url: /Materi
            - img [ref=e67]
            - generic [ref=e70]: Library Materi
        - listitem [ref=e71]:
          - link "Rencana Diklat" [ref=e72] [cursor=pointer]:
            - /url: /RencanaDiklat/RPT/PF
            - img [ref=e74]
            - generic [ref=e78]: Rencana Diklat
        - listitem [ref=e79]:
          - link "Jadwal Diklat" [ref=e80] [cursor=pointer]:
            - /url: /JadwalDiklat/Internal
            - img [ref=e82]
            - generic [ref=e86]: Jadwal Diklat
        - listitem [ref=e87]:
          - link "Evaluasi" [ref=e88] [cursor=pointer]:
            - /url: /Diklat/Evaluasi
            - img [ref=e90]
            - generic [ref=e94]: Evaluasi
        - listitem [ref=e95]:
          - link "Laporan" [ref=e96] [cursor=pointer]:
            - /url: /Laporan/Diklat
            - img [ref=e98]
            - generic [ref=e104]: Laporan
        - listitem [ref=e105]:
          - link "Master Data" [ref=e106] [cursor=pointer]:
            - /url: /MasterData/home
            - img [ref=e108]
            - generic [ref=e113]: Master Data
        - listitem [ref=e114]:
          - link "Whattsapp Settings" [ref=e115] [cursor=pointer]:
            - /url: /Settings
            - img [ref=e117]
            - generic [ref=e121]: Whattsapp Settings
        - listitem [ref=e122]:
          - link "Inbox" [ref=e123] [cursor=pointer]:
            - /url: /HLC/Home/user
            - img [ref=e125]
            - generic [ref=e129]: Inbox
    - generic [ref=e130]:
      - generic [ref=e131]:
        - generic:
          - list
      - list [ref=e132]:
        - listitem [ref=e133]:
          - button "EE EVA EFFENDI" [ref=e134]:
            - generic [ref=e136]: EE
            - generic [ref=e138]: EVA EFFENDI
            - img [ref=e139]
  - main [ref=e142]:
    - navigation [ref=e143]:
      - link "Internal" [ref=e144] [cursor=pointer]:
        - /url: /RencanaDiklat/RPT/PF
      - link "Eksternal" [ref=e145] [cursor=pointer]:
        - /url: /RencanaDiklat/RPT/PN
      - link "HLC" [ref=e146] [cursor=pointer]:
        - /url: /HLC/Home/manajemen
    - generic [ref=e147]:
      - img [ref=e148]
      - generic [ref=e150]:
        - heading "Human Learning Center (HLC)" [level=1] [ref=e151]
        - paragraph [ref=e152]: Kelola data program pelatihan dan detail diklat peserta.
    - generic [ref=e153]:
      - button "Tambah Program" [ref=e155]:
        - img [ref=e156]
        - text: Tambah Program
      - generic [ref=e158]:
        - generic [ref=e159]:
          - generic [ref=e160]: Cari Program
          - generic [ref=e161]:
            - textbox "Ketik nama program..." [ref=e162]
            - img [ref=e163]
        - generic [ref=e165]:
          - generic [ref=e166]: Tahun
          - combobox [ref=e167]:
            - option "Semua Tahun" [selected]
            - option "2026"
        - generic [ref=e168]:
          - generic [ref=e169]: Tampilan
          - combobox [ref=e170]:
            - option "5 baris" [selected]
            - option "10 baris"
            - option "20 baris"
      - generic [ref=e171]: Menampilkan 4 dari 4 program
      - generic [ref=e172]:
        - generic [ref=e173]:
          - generic [ref=e174]:
            - generic [ref=e175]:
              - heading "Internal" [level=3] [ref=e176]
              - generic [ref=e178]: "Tahun: 2026"
            - generic [ref=e179]:
              - button [ref=e180]:
                - img [ref=e181]
              - button [ref=e183]:
                - img [ref=e184]
              - button [ref=e187]:
                - img [ref=e188]
          - table [ref=e193]:
            - rowgroup [ref=e194]:
              - row "Nama Diklat Tanggal Mulai Tanggal Selesai Jam Diklat Status Alasan" [ref=e195]:
                - columnheader "Nama Diklat" [ref=e196]
                - columnheader "Tanggal Mulai" [ref=e197]
                - columnheader "Tanggal Selesai" [ref=e198]
                - columnheader "Jam Diklat" [ref=e199]
                - columnheader "Status" [ref=e200]
                - columnheader "Alasan" [ref=e201]
            - rowgroup [ref=e202]:
              - row "Kebijakan 2026 2026-02-19 2026-02-19 2 jam approved E EVA EFFENDI" [ref=e203]:
                - cell "Kebijakan 2026" [ref=e204]
                - cell "2026-02-19" [ref=e205]
                - cell "2026-02-19" [ref=e206]
                - cell "2 jam" [ref=e207]
                - cell "approved" [ref=e208]
                - cell [ref=e209]
                - cell "E EVA EFFENDI" [ref=e210]:
                  - generic [ref=e211]:
                    - generic [ref=e212]: E
                    - text: EVA EFFENDI
                - cell [ref=e213]:
                  - button [ref=e214]:
                    - img [ref=e215]
                  - button [ref=e218]:
                    - img [ref=e219]
              - row "Lihat undangan 2026-05-30 2026-05-30 3 jam Tolak Lihat Alasan E EVA EFFENDI" [ref=e222]:
                - cell "Lihat undangan" [ref=e223]:
                  - button "Lihat undangan" [ref=e224]
                - cell "2026-05-30" [ref=e225]
                - cell "2026-05-30" [ref=e226]
                - cell "3 jam" [ref=e227]
                - cell "Tolak" [ref=e228]
                - cell "Lihat Alasan" [ref=e229]:
                  - button "Lihat Alasan" [ref=e230]
                - cell "E EVA EFFENDI" [ref=e231]:
                  - generic [ref=e232]:
                    - generic [ref=e233]: E
                    - text: EVA EFFENDI
                - cell [ref=e234]:
                  - button [ref=e235]:
                    - img [ref=e236]
                  - button [ref=e239]:
                    - img [ref=e240]
              - row "Lihat undangan 2026-05-25 2026-05-25 1 jam pending E EVA EFFENDI" [ref=e243]:
                - cell "Lihat undangan" [ref=e244]:
                  - button "Lihat undangan" [ref=e245]
                - cell "2026-05-25" [ref=e246]
                - cell "2026-05-25" [ref=e247]
                - cell "1 jam" [ref=e248]
                - cell "pending" [ref=e249]
                - cell [ref=e250]
                - cell "E EVA EFFENDI" [ref=e251]:
                  - generic [ref=e252]:
                    - generic [ref=e253]: E
                    - text: EVA EFFENDI
                - cell [ref=e254]:
                  - button [ref=e255]:
                    - img [ref=e256]
                  - button [ref=e259]:
                    - img [ref=e260]
              - row "Lihat undangan 2026-05-26 2026-05-28 3 jam approved E EVA EFFENDI" [ref=e263]:
                - cell "Lihat undangan" [ref=e264]:
                  - button "Lihat undangan" [ref=e265]
                - cell "2026-05-26" [ref=e266]
                - cell "2026-05-28" [ref=e267]
                - cell "3 jam" [ref=e268]
                - cell "approved" [ref=e269]
                - cell [ref=e270]
                - cell "E EVA EFFENDI" [ref=e271]:
                  - generic [ref=e272]:
                    - generic [ref=e273]: E
                    - text: EVA EFFENDI
                - cell [ref=e274]:
                  - button [ref=e275]:
                    - img [ref=e276]
                  - button [ref=e279]:
                    - img [ref=e280]
              - row "Lihat undangan 2026-06-02 2026-06-03 18 jam approved E EVA EFFENDI" [ref=e283]:
                - cell "Lihat undangan" [ref=e284]:
                  - button "Lihat undangan" [ref=e285]
                - cell "2026-06-02" [ref=e286]
                - cell "2026-06-03" [ref=e287]
                - cell "18 jam" [ref=e288]
                - cell "approved" [ref=e289]
                - cell [ref=e290]
                - cell "E EVA EFFENDI" [ref=e291]:
                  - generic [ref=e292]:
                    - generic [ref=e293]: E
                    - text: EVA EFFENDI
                - cell [ref=e294]:
                  - button [ref=e295]:
                    - img [ref=e296]
                  - button [ref=e299]:
                    - img [ref=e300]
        - generic [ref=e303]:
          - generic [ref=e304]:
            - generic [ref=e305]:
              - heading "Formal Program" [level=3] [ref=e306]
              - generic [ref=e308]: "Tahun: 2026"
            - generic [ref=e309]:
              - button [ref=e310]:
                - img [ref=e311]
              - button [ref=e313]:
                - img [ref=e314]
              - button [ref=e317]:
                - img [ref=e318]
          - table [ref=e323]:
            - rowgroup [ref=e324]:
              - row "Nama Diklat Tanggal Mulai Tanggal Selesai Jam Diklat Status Alasan" [ref=e325]:
                - columnheader "Nama Diklat" [ref=e326]
                - columnheader "Tanggal Mulai" [ref=e327]
                - columnheader "Tanggal Selesai" [ref=e328]
                - columnheader "Jam Diklat" [ref=e329]
                - columnheader "Status" [ref=e330]
                - columnheader "Alasan" [ref=e331]
            - rowgroup [ref=e332]:
              - row "Pelatihan Public Speaking 2026-01-21 2026-01-21 2 jam approved E EVA EFFENDI" [ref=e333]:
                - cell "Pelatihan Public Speaking" [ref=e334]
                - cell "2026-01-21" [ref=e335]
                - cell "2026-01-21" [ref=e336]
                - cell "2 jam" [ref=e337]
                - cell "approved" [ref=e338]
                - cell [ref=e339]
                - cell "E EVA EFFENDI" [ref=e340]:
                  - generic [ref=e341]:
                    - generic [ref=e342]: E
                    - text: EVA EFFENDI
                - cell [ref=e343]:
                  - button [ref=e344]:
                    - img [ref=e345]
                  - button [ref=e348]:
                    - img [ref=e349]
              - row "pelatihan manajerial klinis 2026-01-21 2026-01-21 2 jam approved A ARIF JUNIANTO" [ref=e352]:
                - cell "pelatihan manajerial klinis" [ref=e353]
                - cell "2026-01-21" [ref=e354]
                - cell "2026-01-21" [ref=e355]
                - cell "2 jam" [ref=e356]
                - cell "approved" [ref=e357]
                - cell [ref=e358]
                - cell "A ARIF JUNIANTO" [ref=e359]:
                  - generic [ref=e360]:
                    - generic [ref=e361]: A
                    - text: ARIF JUNIANTO
                - cell [ref=e362]:
                  - button [ref=e363]:
                    - img [ref=e364]
                  - button [ref=e367]:
                    - img [ref=e368]
              - row "Diklat Karyawan 2026-05-19 2026-05-19 1 jam approved E EVA EFFENDI" [ref=e371]:
                - cell "Diklat Karyawan" [ref=e372]
                - cell "2026-05-19" [ref=e373]
                - cell "2026-05-19" [ref=e374]
                - cell "1 jam" [ref=e375]
                - cell "approved" [ref=e376]
                - cell [ref=e377]
                - cell "E EVA EFFENDI" [ref=e378]:
                  - generic [ref=e379]:
                    - generic [ref=e380]: E
                    - text: EVA EFFENDI
                - cell [ref=e381]:
                  - button [ref=e382]:
                    - img [ref=e383]
                  - button [ref=e386]:
                    - img [ref=e387]
              - row "Diklat Karyawan 2026-05-24 2026-05-24 1 jam rejected E EVA EFFENDI" [ref=e390]:
                - cell "Diklat Karyawan" [ref=e391]
                - cell "2026-05-24" [ref=e392]
                - cell "2026-05-24" [ref=e393]
                - cell "1 jam" [ref=e394]
                - cell "rejected" [ref=e395]
                - cell [ref=e396]
                - cell "E EVA EFFENDI" [ref=e397]:
                  - generic [ref=e398]:
                    - generic [ref=e399]: E
                    - text: EVA EFFENDI
                - cell [ref=e400]:
                  - button [ref=e401]:
                    - img [ref=e402]
                  - button [ref=e405]:
                    - img [ref=e406]
              - row "Diklat Karyawan 2026-05-19 2026-05-19 1 jam rejected E EVA EFFENDI" [ref=e409]:
                - cell "Diklat Karyawan" [ref=e410]
                - cell "2026-05-19" [ref=e411]
                - cell "2026-05-19" [ref=e412]
                - cell "1 jam" [ref=e413]
                - cell "rejected" [ref=e414]
                - cell [ref=e415]
                - cell "E EVA EFFENDI" [ref=e416]:
                  - generic [ref=e417]:
                    - generic [ref=e418]: E
                    - text: EVA EFFENDI
                - cell [ref=e419]:
                  - button [ref=e420]:
                    - img [ref=e421]
                  - button [ref=e424]:
                    - img [ref=e425]
              - row "Lihat undangan 2026-06-02 2026-06-02 1 jam approved E EVA EFFENDI" [ref=e428]:
                - cell "Lihat undangan" [ref=e429]:
                  - button "Lihat undangan" [ref=e430]
                - cell "2026-06-02" [ref=e431]
                - cell "2026-06-02" [ref=e432]
                - cell "1 jam" [ref=e433]
                - cell "approved" [ref=e434]
                - cell [ref=e435]
                - cell "E EVA EFFENDI" [ref=e436]:
                  - generic [ref=e437]:
                    - generic [ref=e438]: E
                    - text: EVA EFFENDI
                - cell [ref=e439]:
                  - button [ref=e440]:
                    - img [ref=e441]
                  - button [ref=e444]:
                    - img [ref=e445]
        - generic [ref=e448]:
          - generic [ref=e449]:
            - generic [ref=e450]:
              - heading "Public Speaking" [level=3] [ref=e451]
              - generic [ref=e453]: "Tahun: 2026"
            - generic [ref=e454]:
              - button [ref=e455]:
                - img [ref=e456]
              - button [ref=e458]:
                - img [ref=e459]
              - button [ref=e462]:
                - img [ref=e463]
          - table [ref=e468]:
            - rowgroup [ref=e469]:
              - row "Nama Diklat Tanggal Mulai Tanggal Selesai Jam Diklat Status Alasan" [ref=e470]:
                - columnheader "Nama Diklat" [ref=e471]
                - columnheader "Tanggal Mulai" [ref=e472]
                - columnheader "Tanggal Selesai" [ref=e473]
                - columnheader "Jam Diklat" [ref=e474]
                - columnheader "Status" [ref=e475]
                - columnheader "Alasan" [ref=e476]
            - rowgroup [ref=e477]:
              - row "Diklat Public Speaking RSMDH 2026-05-06 2026-05-06 2 jam approved E EVA EFFENDI" [ref=e478]:
                - cell "Diklat Public Speaking RSMDH" [ref=e479]
                - cell "2026-05-06" [ref=e480]
                - cell "2026-05-06" [ref=e481]
                - cell "2 jam" [ref=e482]
                - cell "approved" [ref=e483]
                - cell [ref=e484]
                - cell "E EVA EFFENDI" [ref=e485]:
                  - generic [ref=e486]:
                    - generic [ref=e487]: E
                    - text: EVA EFFENDI
                - cell [ref=e488]:
                  - button [ref=e489]:
                    - img [ref=e490]
                  - button [ref=e493]:
                    - img [ref=e494]
              - row "Teknik Observatif 2026-05-10 2026-05-11 4 jam approved E EVA EFFENDI" [ref=e497]:
                - cell "Teknik Observatif" [ref=e498]
                - cell "2026-05-10" [ref=e499]
                - cell "2026-05-11" [ref=e500]
                - cell "4 jam" [ref=e501]
                - cell "approved" [ref=e502]
                - cell [ref=e503]
                - cell "E EVA EFFENDI" [ref=e504]:
                  - generic [ref=e505]:
                    - generic [ref=e506]: E
                    - text: EVA EFFENDI
                - cell [ref=e507]:
                  - button [ref=e508]:
                    - img [ref=e509]
                  - button [ref=e512]:
                    - img [ref=e513]
              - row "Lihat undangan 2026-05-27 2026-05-28 4 jam approved M MUHAMMAD NUR SALAM" [ref=e516]:
                - cell "Lihat undangan" [ref=e517]:
                  - button "Lihat undangan" [ref=e518]
                - cell "2026-05-27" [ref=e519]
                - cell "2026-05-28" [ref=e520]
                - cell "4 jam" [ref=e521]
                - cell "approved" [ref=e522]
                - cell [ref=e523]
                - cell "M MUHAMMAD NUR SALAM" [ref=e524]:
                  - generic [ref=e525]:
                    - generic [ref=e526]: M
                    - text: MUHAMMAD NUR SALAM
                - cell [ref=e527]:
                  - button [ref=e528]:
                    - img [ref=e529]
                  - button [ref=e532]:
                    - img [ref=e533]
              - row "Lihat undangan 2026-05-28 2026-05-29 2 jam menunggu_persetujuan E EVA EFFENDI" [ref=e536]:
                - cell "Lihat undangan" [ref=e537]:
                  - button "Lihat undangan" [ref=e538]
                - cell "2026-05-28" [ref=e539]
                - cell "2026-05-29" [ref=e540]
                - cell "2 jam" [ref=e541]
                - cell "menunggu_persetujuan" [ref=e542]
                - cell [ref=e543]
                - cell "E EVA EFFENDI" [ref=e544]:
                  - generic [ref=e545]:
                    - generic [ref=e546]: E
                    - text: EVA EFFENDI
                - cell [ref=e547]:
                  - button [ref=e548]:
                    - img [ref=e549]
                  - button [ref=e552]:
                    - img [ref=e553]
              - row "Diklat Presenter 2026-05-17 2026-05-17 1 jam approved E EVA EFFENDI" [ref=e556]:
                - cell "Diklat Presenter" [ref=e557]
                - cell "2026-05-17" [ref=e558]
                - cell "2026-05-17" [ref=e559]
                - cell "1 jam" [ref=e560]
                - cell "approved" [ref=e561]
                - cell [ref=e562]
                - cell "E EVA EFFENDI" [ref=e563]:
                  - generic [ref=e564]:
                    - generic [ref=e565]: E
                    - text: EVA EFFENDI
                - cell [ref=e566]:
                  - button [ref=e567]:
                    - img [ref=e568]
                  - button [ref=e571]:
                    - img [ref=e572]
              - row "Public Learning 2026-05-11 2026-05-11 1 jam approved E EVA EFFENDI" [ref=e575]:
                - cell "Public Learning" [ref=e576]
                - cell "2026-05-11" [ref=e577]
                - cell "2026-05-11" [ref=e578]
                - cell "1 jam" [ref=e579]
                - cell "approved" [ref=e580]
                - cell [ref=e581]
                - cell "E EVA EFFENDI" [ref=e582]:
                  - generic [ref=e583]:
                    - generic [ref=e584]: E
                    - text: EVA EFFENDI
                - cell [ref=e585]:
                  - button [ref=e586]:
                    - img [ref=e587]
                  - button [ref=e590]:
                    - img [ref=e591]
        - generic [ref=e594]:
          - generic [ref=e595]:
            - generic [ref=e596]:
              - heading "Diklat Kebersihan" [level=3] [ref=e597]
              - generic [ref=e599]: "Tahun: 2026"
            - generic [ref=e600]:
              - button [ref=e601]:
                - img [ref=e602]
              - button [ref=e604]:
                - img [ref=e605]
              - button [ref=e608]:
                - img [ref=e609]
          - table [ref=e614]:
            - rowgroup [ref=e615]:
              - row "Nama Diklat Tanggal Mulai Tanggal Selesai Jam Diklat Status Alasan" [ref=e616]:
                - columnheader "Nama Diklat" [ref=e617]
                - columnheader "Tanggal Mulai" [ref=e618]
                - columnheader "Tanggal Selesai" [ref=e619]
                - columnheader "Jam Diklat" [ref=e620]
                - columnheader "Status" [ref=e621]
                - columnheader "Alasan" [ref=e622]
            - rowgroup [ref=e623]:
              - row "Pelatihan Kebersihan 2026-05-10 2026-05-10 1 jam approved E EVA EFFENDI" [ref=e624]:
                - cell "Pelatihan Kebersihan" [ref=e625]
                - cell "2026-05-10" [ref=e626]
                - cell "2026-05-10" [ref=e627]
                - cell "1 jam" [ref=e628]
                - cell "approved" [ref=e629]
                - cell [ref=e630]
                - cell "E EVA EFFENDI" [ref=e631]:
                  - generic [ref=e632]:
                    - generic [ref=e633]: E
                    - text: EVA EFFENDI
                - cell [ref=e634]:
                  - button [ref=e635]:
                    - img [ref=e636]
                  - button [ref=e639]:
                    - img [ref=e640]
              - row "Diklat Klinis 2026-05-15 2026-05-15 2 jam approved E EVA EFFENDI" [ref=e643]:
                - cell "Diklat Klinis" [ref=e644]
                - cell "2026-05-15" [ref=e645]
                - cell "2026-05-15" [ref=e646]
                - cell "2 jam" [ref=e647]
                - cell "approved" [ref=e648]
                - cell [ref=e649]
                - cell "E EVA EFFENDI" [ref=e650]:
                  - generic [ref=e651]:
                    - generic [ref=e652]: E
                    - text: EVA EFFENDI
                - cell [ref=e653]:
                  - button [ref=e654]:
                    - img [ref=e655]
                  - button [ref=e658]:
                    - img [ref=e659]
              - row "Klinis part2 2026-05-15 2026-05-15 2 jam approved E EVA EFFENDI" [ref=e662]:
                - cell "Klinis part2" [ref=e663]
                - cell "2026-05-15" [ref=e664]
                - cell "2026-05-15" [ref=e665]
                - cell "2 jam" [ref=e666]
                - cell "approved" [ref=e667]
                - cell [ref=e668]
                - cell "E EVA EFFENDI" [ref=e669]:
                  - generic [ref=e670]:
                    - generic [ref=e671]: E
                    - text: EVA EFFENDI
                - cell [ref=e672]:
                  - button [ref=e673]:
                    - img [ref=e674]
                  - button [ref=e677]:
                    - img [ref=e678]
              - row "Lihat undangan 2026-05-28 2026-05-29 4 jam approved S SURYAN LESTIANTO" [ref=e681]:
                - cell "Lihat undangan" [ref=e682]:
                  - button "Lihat undangan" [ref=e683]
                - cell "2026-05-28" [ref=e684]
                - cell "2026-05-29" [ref=e685]
                - cell "4 jam" [ref=e686]
                - cell "approved" [ref=e687]
                - cell [ref=e688]
                - cell "S SURYAN LESTIANTO" [ref=e689]:
                  - generic [ref=e690]:
                    - generic [ref=e691]: S
                    - text: SURYAN LESTIANTO
                - cell [ref=e692]:
                  - button [ref=e693]:
                    - img [ref=e694]
                  - button [ref=e697]:
                    - img [ref=e698]
              - row "Diklat Cleaning 2026-05-19 2026-05-19 1 jam approved M MUHAMMAD NUR SALAM" [ref=e701]:
                - cell "Diklat Cleaning" [ref=e702]
                - cell "2026-05-19" [ref=e703]
                - cell "2026-05-19" [ref=e704]
                - cell "1 jam" [ref=e705]
                - cell "approved" [ref=e706]
                - cell [ref=e707]
                - cell "M MUHAMMAD NUR SALAM" [ref=e708]:
                  - generic [ref=e709]:
                    - generic [ref=e710]: M
                    - text: MUHAMMAD NUR SALAM
                - cell [ref=e711]:
                  - button [ref=e712]:
                    - img [ref=e713]
                  - button [ref=e716]:
                    - img [ref=e717]
      - generic [ref=e722]:
        - heading "Tambah Program" [level=3] [ref=e724]
        - generic [ref=e725]:
          - generic [ref=e726]:
            - generic [ref=e727]: Nama Program
            - textbox [active] [ref=e728]
          - generic [ref=e729]:
            - generic [ref=e730]: Tahun
            - spinbutton [ref=e731]: "2026"
        - generic [ref=e732]:
          - button "Batal" [ref=e733]
          - button "Simpan" [ref=e734]
```

# Test source

```ts
  1  | import { expect, test } from '@playwright/test';
  2  | 
  3  | test('Program HLC Kosong', async ({ page }) => {
  4  |     // Login
  5  |     await page.goto('http://localhost:8000/login');
  6  | 
  7  |     await page
  8  |         .getByRole('textbox', { name: 'Enter your Employee ID' })
  9  |         .fill('005100439');
  10 | 
  11 |     await page
  12 |         .getByRole('textbox', { name: 'Enter Password' })
  13 |         .fill('005100439');
  14 | 
  15 |     await page.getByRole('button', { name: 'Sign in →' }).click();
  16 | 
  17 |     // Tunggu dashboard muncul
  18 |     await expect(page).toHaveURL(/dashboard/);
  19 | 
  20 |     // Klik menu Rencana Diklat
  21 |     await page.getByRole('link', { name: 'Rencana Diklat' }).click();
  22 | 
  23 |     // Tunggu navbar HLC muncul
  24 |     const hlcLink = page.locator('a[href="/HLC/Home/manajemen"]');
  25 | 
  26 |     await expect(hlcLink).toBeVisible({
  27 |         timeout: 10000,
  28 |     });
  29 | 
  30 |     await hlcLink.click();
  31 | 
  32 |     // Pastikan benar-benar masuk halaman HLC
  33 |     await expect(page).toHaveURL(/HLC\/Home\/manajemen/);
  34 | 
  35 |     // Verifikasi heading HLC tampil
  36 |     await expect(
  37 |         page.getByRole('heading', {
  38 |             name: 'Human Learning Center (HLC)',
  39 |         }),
  40 |     ).toBeVisible();
  41 | 
  42 |     // Klik tambah program
  43 |     await page.getByRole('button', { name: 'Tambah Program' }).click();
  44 | 
  45 |     // DEBUG
  46 |     await page.pause();
  47 | 
  48 |     await page.getByRole('textbox').nth(1).click();
> 49 |     await namaProgram.click();
     |     ^ ReferenceError: namaProgram is not defined
  50 |     await namaProgram.fill('Test Program');
  51 | 
  52 |     // Jika modal muncul
  53 |     const simpanButton = page.getByRole('button', {
  54 |         name: 'Simpan',
  55 |     });
  56 | 
  57 |     await expect(simpanButton).toBeVisible();
  58 | 
  59 |     await simpanButton.click();
  60 | 
  61 |     await expect(page.getByText('Nama program wajib diisi')).toBeVisible();
  62 | });
  63 | 
```