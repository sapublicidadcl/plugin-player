document.addEventListener('DOMContentLoaded', function() {
    var audio = new Audio(audio_player_vars.stream_url);
    var playPauseButton = document.getElementById('play-pause-button');
    var volumeSlider = document.getElementById('volume-slider');

    playPauseButton.addEventListener('click', function() {
        if (audio.paused) {
            audio.play();
            playPauseButton.innerHTML = '<i class="fas fa-pause"></i>';
            playPauseButton.classList.add('playing');
        } else {
            audio.pause();
            playPauseButton.innerHTML = '<i class="fas fa-play"></i>';
            playPauseButton.classList.remove('playing');
        }
    });

    volumeSlider.addEventListener('input', function() {
        audio.volume = volumeSlider.value;
    });
});
