create sequence user_details_detail_id_seq;

alter sequence user_details_detail_id_seq owner to admin;

create sequence announcement_announcement_id_seq;

alter sequence announcement_announcement_id_seq owner to admin;

create sequence support_message_message_id_seq;

alter sequence support_message_message_id_seq owner to admin;

create table users
(
    user_id       serial
        primary key,
    email         varchar(100)                           not null
        constraint users_pk
            unique,
    password_hash varchar(255)                           not null,
    created_at    timestamp with time zone default CURRENT_TIMESTAMP,
    is_admin      boolean                  default false not null,
    phone         text                                   not null
        constraint users_pk_2
            unique
        constraint users_pk_3
            unique
);

alter table users
    owner to admin;

create table user_detail
(
    detail_id   integer generated always as identity
        constraint user_details_pk
            primary key,
    user_id     integer not null
        constraint user_details_users_user_id_fk
            references users,
    name        text    not null,
    surname     text    not null,
    avatar_name text
);

alter table user_detail
    owner to admin;

alter sequence user_details_detail_id_seq owned by user_detail.detail_id;

create table animal_types
(
    type_id   integer generated always as identity
        constraint animal_types_pk
            primary key,
    type_name text not null
);

alter table animal_types
    owner to admin;

create table animal_features
(
    feature_id   integer generated always as identity
        constraint animal_features_pk
            primary key,
    feature_name text not null
);

alter table animal_features
    owner to admin;

create table announcements
(
    announcement_id integer generated always as identity
        constraint announcements_pk
            primary key,
    type_id         integer
        constraint announcements_animal_types_type_id_fk
            references animal_types
            on delete set null,
    user_id         integer
        constraint announcements_users_user_id_fk
            references users,
    accepted        boolean                  default false             not null,
    created_at      timestamp with time zone default CURRENT_TIMESTAMP not null,
    last_modified   timestamp                default CURRENT_TIMESTAMP not null
);

alter table announcements
    owner to admin;

alter sequence announcement_announcement_id_seq owned by announcements.announcement_id;

create table announcement_detail
(
    announcement_detail_id integer generated always as identity
        constraint announcement_detail_pk
            primary key,
    announcement_id        integer
        constraint announcement_detail_announcement_announcement_id_fk
            references announcements,
    animal_name            text not null,
    animal_locality        text not null,
    animal_price           integer,
    animal_description     varchar(2000),
    animal_age             integer,
    animal_gender          text not null,
    animal_avatar_name     text,
    animal_kind            text,
    animal_age_type        text
);

alter table announcement_detail
    owner to admin;

create table announcement_animal_features
(
    aa_id                  integer generated always as identity
        constraint announcement_animal_features_pk
            primary key,
    feature_id             integer not null
        constraint announcement_animal_features_animal_features_feature_id_fk
            references animal_features
            on delete cascade,
    value                  boolean not null,
    announcement_detail_id integer not null
        constraint announcement_animal_features_announcement_detail_announcement_d
            references announcement_detail
);

alter table announcement_animal_features
    owner to admin;

create table announcement_likes
(
    like_id         integer generated always as identity
        constraint announcement_likes_pk
            primary key,
    user_id         integer                             not null
        constraint announcement_likes_users_user_id_fk
            references users,
    announcement_id integer                             not null
        constraint announcement_likes_announcement_announcement_id_fk
            references announcements,
    given_at        timestamp default CURRENT_TIMESTAMP not null
);

alter table announcement_likes
    owner to admin;

create table announcement_report
(
    report_id       integer generated always as identity
        constraint announcement_report_pk
            primary key,
    user_id         integer                             not null
        constraint announcement_report_users_user_id_fk
            references users,
    announcement_id integer                             not null
        constraint announcement_report_announcement_announcement_id_fk
            references announcements,
    details         varchar(1000),
    given           timestamp default CURRENT_TIMESTAMP not null,
    checked         boolean   default false             not null
);

alter table announcement_report
    owner to admin;

create unique index announcement_report_user_id_announcement_id_uindex
    on announcement_report (user_id, announcement_id);

create table support
(
    ticket_id integer generated always as identity
        constraint support_pk
            primary key,
    user_id   integer                           not null
        constraint support_users_user_id_fk
            references users,
    created   date    default CURRENT_TIMESTAMP not null,
    solved    boolean default false             not null
);

alter table support
    owner to admin;

create table support_messages
(
    message_id integer generated always as identity
        constraint support_messages_pk
            primary key,
    user_id    integer
        constraint support_messages_users_user_id_fk
            references users,
    sent       date default CURRENT_TIMESTAMP,
    content    varchar(1000) not null,
    ticket_id  integer
        constraint support_messages_support_ticket_id_fk
            references support
);

alter table support_messages
    owner to admin;

alter sequence support_message_message_id_seq owned by support_messages.message_id;

create table deleted_announcements
(
    delete_id       integer generated always as identity
        constraint deleted_announcements_pk
            primary key,
    announcement_id integer                             not null
        constraint deleted_announcements_pk_2
            unique
        constraint deleted_announcements_announcements_announcement_id_fk
            references announcements,
    deleted_at      timestamp default CURRENT_TIMESTAMP not null,
    violated        boolean   default false             not null,
    admin_id        integer
        constraint deleted_announcements_users_user_id_fk
            references users
);

alter table deleted_announcements
    owner to admin;

create view animal_types_popularity(type_name, usage_count) as
SELECT animal_types.type_name,
       count(a.announcement_id) AS usage_count
FROM animal_types
         LEFT JOIN announcements a ON animal_types.type_id = a.type_id
GROUP BY animal_types.type_name
ORDER BY (count(a.announcement_id)) DESC;

alter table animal_types_popularity
    owner to admin;

create view "frequently-listed-features"(feature_name, feature_count) as
SELECT af.feature_name,
       count(*) AS feature_count
FROM announcement_animal_features aaf
         JOIN animal_features af ON aaf.feature_id = af.feature_id
GROUP BY af.feature_name
ORDER BY (count(*)) DESC
LIMIT 5;

alter table "frequently-listed-features"
    owner to admin;

create function update_last_modified() returns trigger
    language plpgsql
as
$$
BEGIN
    NEW.last_modified = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$;

alter function update_last_modified() owner to admin;

create trigger update_announcement_last_modified
    before update
    on announcements
    for each row
execute procedure update_last_modified();

create function delete_full_announcement(announcementid integer) returns void
    language plpgsql
as
$$
BEGIN
    -- Usunięcie wpisów z announcement_likes
    DELETE FROM announcement_likes WHERE announcement_id = announcementId;

    -- Usunięcie wpisów z announcement_report
    DELETE FROM announcement_report WHERE announcement_id = announcementId;

    -- Usunięcie wpisu z deleted_announcements
    DELETE FROM deleted_announcements WHERE announcement_id = announcementId;

    -- Usunięcie wpisów z announcement_animal_features
    DELETE FROM announcement_animal_features
    WHERE announcement_detail_id IN (
        SELECT announcement_detail_id FROM announcement_detail WHERE announcement_id = announcementId
    );

    -- Usunięcie wpisu z announcement_detail
    DELETE FROM announcement_detail WHERE announcement_id = announcementId;

    -- Usunięcie wpisu z tabeli głównej announcements
    DELETE FROM announcements WHERE announcement_id = announcementId;

EXCEPTION
    WHEN OTHERS THEN
        -- W przypadku błędu, wykonaj rollback
        RAISE;
END;
$$;

alter function delete_full_announcement(integer) owner to admin;


