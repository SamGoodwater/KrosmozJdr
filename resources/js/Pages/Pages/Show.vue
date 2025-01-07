<script setup>
defileProps({
    page: {
        type: Array,
        required: true
    }
});
</script>

<template>

    <Head title="{{ page.name }}">
        <meta head-key="description" name="description" content="Page du site KrosmozJDR : {{ page.name }}" />
        <meta head-key="keywords" name="keywords" content="Accueil, JDR, Jeu de Rôle, Dofus" />
    </Head>

</template>
