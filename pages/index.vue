<template class="bg-black">
  <main class="flex flex-col justify-center mx-auto p-5 w-full xl:w-1/2">
    <h1 class="bg-gray-300 mb-4 mt-4 p-4 rounded">
      <span class="font-black text-3xl uppercase">MuseumBots</span><br />
      <span class="text-sm">
        Projets de petits bots Twitter culturels, muséaux et patrimoniaux.<br />
        Projects of small Twitter bots about culture, museums and heritage.
      </span>
    </h1>
    <ul>
      <li class="bg-gray-100 mb-4 p-4 rounded">
        <span class=text-xl>
          <a href="https://twitter.com/patchedmet" class="font-bold no-underline hover:underline" target="_blank" rel="nooepener">PatchedMET</a>
        </span><br />
        <span class="text-sm">
          A bot by <a href="https://twitter.com/papposilene" class="no-underline hover:underline" target="_blank" rel="nooepener">@papposilene</a> that combines great artworks from the
          <a href="https://www.metmuseum.org/" class="no-underline hover:underline" target="_blank" rel="nooepener">Metropolitan Museum of Art</a> with video games patch notes
          (Crusader Kings 3, The Sims 4, World of Warcraft, etc.).
          Inspired by <a href="https://twitter.com/litpatches_txt" class="no-underline hover:underline" target="_blank" rel="nooepener">@litpatches_txt</a>,
          created by <a href="https://twitter.com/lizardengland" class="no-underline hover:underline" target="_blank" rel="nooepener">@lizardengland</a>.
        </span>
      </li>
      <li class="bg-gray-100 mb-4 p-4 rounded">
        <span class=text-xl>
          <a href="https://twitter.com/louvrobot" class="font-bold no-underline hover:underline" target="_blank" rel="nooepener">LouvrBot</a>
        </span><br />
        <span class="text-sm">
          Je suis un bot qui tweete des oeuvres du <a href="https://www.louvre.fr/" class="no-underline hover:underline" target="_blank" rel="nooepener">@museelouvre</a>
          en allant les récupérer sur la base de données <a href="https://www.louvre.fr/" class="no-underline hover:underline" target="_blank" rel="nooepener">Atlas</a>,
          (dans l'espoir et l'attente qu'un jour le musée du Louvre (sans parler des autres musées français) dispose de son API).
          En tout cas, n'hésitez pas à venir les admirer sur place, dans les salles du musée !
        </span>
      </li>
    </ul>
    <h5 class="text-black dark:text-white p-4 text-xs text-right">
      Encore un <a href="https://github.com/papposilene/museumbot" class="no-underline hover:underline" target="_blank" rel="nooepener">bien beau projet de Philippe-Alexandre</a>.<br />
      Frontend with NuxtJS and Tailwind CSS.
    </h5>
  </main>
</template>

<script>
export default {
  colorMode: 'dark',
}
</script>
