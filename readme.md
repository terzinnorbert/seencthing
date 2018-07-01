# Seencthing

Seencthing is an open-source web viewer for [Syncthing](https://syncthing.net).  
It does not store the shared files in the filesystem, just lists the remote directories and when you click on the file it downloads in the background.

It uses the syncthing binary, make sure you installed the [latest version](https://syncthing.net)

A side effect of this implementation: you have to run one or more other instances where the shared data are available. Without online instance(s) you just see the filesystem.

---

![Seencthing](https://raw.githubusercontent.com/wiki/terzinnorbert/seencthing/seencthing.gif)
