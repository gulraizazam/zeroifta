import { DataTypes, Model } from 'sequelize';
import sequelize from '../config/database.js';

class FuelStation extends Model {}

FuelStation.init({
    id: {
        type: DataTypes.BIGINT,
        primaryKey: true,
        autoIncrement: true
    },
    name: {
        type: DataTypes.STRING,
        allowNull: false
    },
    address: {
        type: DataTypes.STRING,
        allowNull: true
    },
    city: {
        type: DataTypes.STRING,
        allowNull: true
    },
    state: {
        type: DataTypes.STRING,
        allowNull: true
    },
    zip: {
        type: DataTypes.STRING,
        allowNull: true
    },
    country: {
        type: DataTypes.STRING,
        allowNull: true
    },
    latitude: {
        type: DataTypes.DECIMAL(20, 8),
        allowNull: true
    },
    longitude: {
        type: DataTypes.DECIMAL(20, 8),
        allowNull: true
    },
    phone: {
        type: DataTypes.STRING,
        allowNull: true
    },
    email: {
        type: DataTypes.STRING,
        allowNull: true
    },
    website: {
        type: DataTypes.STRING,
        allowNull: true
    },
    hours: {
        type: DataTypes.STRING,
        allowNull: true
    },
    amenities: {
        type: DataTypes.TEXT,
        allowNull: true
    },
    fuel_types: {
        type: DataTypes.TEXT,
        allowNull: true
    },
    payment_methods: {
        type: DataTypes.TEXT,
        allowNull: true
    },
    status: {
        type: DataTypes.STRING,
        allowNull: true
    },
    last_updated: {
        type: DataTypes.DATE,
        allowNull: true
    },
    station_id: {
        type: DataTypes.STRING,
        allowNull: true,
        unique: true
    },
    brand: {
        type: DataTypes.STRING,
        allowNull: true
    },
    network: {
        type: DataTypes.STRING,
        allowNull: true
    },
    price_regular: {
        type: DataTypes.DECIMAL(10, 2),
        allowNull: true
    },
    price_premium: {
        type: DataTypes.DECIMAL(10, 2),
        allowNull: true
    },
    price_diesel: {
        type: DataTypes.DECIMAL(10, 2),
        allowNull: true
    },
    price_e85: {
        type: DataTypes.DECIMAL(10, 2),
        allowNull: true
    },
    price_lpg: {
        type: DataTypes.DECIMAL(10, 2),
        allowNull: true
    },
    price_cng: {
        type: DataTypes.DECIMAL(10, 2),
        allowNull: true
    },
    price_e15: {
        type: DataTypes.DECIMAL(10, 2),
        allowNull: true
    },
    price_bd: {
        type: DataTypes.DECIMAL(10, 2),
        allowNull: true
    },
    price_midgrade: {
        type: DataTypes.DECIMAL(10, 2),
        allowNull: true
    }
}, {
    sequelize,
    modelName: 'FuelStation',
    tableName: 'fuel_stations',
    timestamps: true,
    underscored: true,
    indexes: [
        {
            fields: ['latitude', 'longitude']
        },
        {
            fields: ['station_id']
        }
    ]
});

export default FuelStation; 