FROM rust:1.77

RUN apt update && apt install -y clang cmake git pkg-config libssl-dev

WORKDIR /sui
RUN git clone https://github.com/MystenLabs/sui.git
WORKDIR /sui/sui
RUN cargo build --release

ENV PATH="/sui/sui/target/release:$PATH"

