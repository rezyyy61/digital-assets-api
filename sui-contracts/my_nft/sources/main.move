module my_nft::nft {

    use sui::object::{Self, UID};
    use sui::tx_context::{Self, TxContext};
    use sui::transfer;

    public struct MyNFT has key {
    id: UID,
    name: vector<u8>,
    description: vector<u8>,
}

    public entry fun mint(name: vector<u8>, description: vector<u8>, ctx: &mut TxContext) {
        let nft = MyNFT {
            id: object::new(ctx),
            name,
            description,
        };
        transfer::transfer(nft, tx_context::sender(ctx));
    }
}
