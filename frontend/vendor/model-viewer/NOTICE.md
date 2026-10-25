# Lokal eingebundene 3D-Ansicht

- Google model-viewer 4.4.0: https://github.com/google/model-viewer, Apache-2.0. Unverändertes Browser-Bundle `dist/model-viewer.min.js` aus dem offiziellen npm-Paket. Lizenz: `LICENSE-model-viewer.txt`.
- Three.js 0.183.2: https://github.com/mrdoob/three.js, MIT. Lizenz: `LICENSE-three.txt`.
- Lit, lit-html, lit-element und @lit/reactive-element: https://github.com/lit/lit, BSD-3-Clause. Lizenztexte liegen in diesem Ordner.
- Draco glTF-Decoder: https://github.com/google/draco, Apache-2.0. Aus `three/examples/jsm/libs/draco/gltf`, Lizenztext: `LICENSE-model-viewer.txt` (Apache-2.0).
- Basis Universal Transcoder: https://github.com/BinomialLLC/basis_universal, Apache-2.0. Aus `three/examples/jsm/libs/basis`, Lizenztext: `LICENSE-model-viewer.txt` (Apache-2.0). Die Hinweise im Decoder und README sind enthalten.
- Meshoptimizer 1.0: https://github.com/zeux/meshoptimizer, MIT. Aus dem Three.js-Paket. Für das Laden als klassisches Skript wurde nur der abschliessende ES-Modul-Export durch `self.MeshoptDecoder = MeshoptDecoder` ersetzt. Lizenz: `LICENSE-meshoptimizer.txt`.

Das Theme lädt Viewer und Decoder vom eigenen Hosting. Es verwendet keinen externen Viewer-Dienst und keine externen Demo-Modelle. Das Modell und eventuelle eingebettete Texturen werden erst nach dem Klick auf «3D ansehen» geladen. Die Bibliothek wird nur einmal pro Seitenbesuch geladen.
