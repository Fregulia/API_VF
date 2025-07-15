const newman = require('newman');

// Execução automática da coleção
newman.run({
    collection: './API_Esportiva.postman_collection.json',
    environment: './environments/Local.postman_environment.json',
    reporters: ['cli', 'json'],
    reporter: {
        json: {
            export: './results/test-results.json'
        }
    }
}, function (err) {
    if (err) { 
        throw err; 
    }
    console.log('Collection run complete!');
});

// Para produção, altere para:
// environment: './environments/Production.postman_environment.json'