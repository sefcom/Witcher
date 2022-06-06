console.log("\t\tIn test-require.js");
exports = module.exports;
console.log("\t\texported exports");
function testFunction(){
    console.log("\t\t\tIn test function");
}
exports.test = testFunction
console.log("\t\tFinished test-require.js");

