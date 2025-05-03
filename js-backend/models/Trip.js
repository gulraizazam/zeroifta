import { DataTypes, Model } from 'sequelize';
import sequelize from '../config/database.js';

class Trip extends Model {}

Trip.init({
    id: {
        type: DataTypes.BIGINT,
        primaryKey: true,
        autoIncrement: true
    },
    user_id: {
        type: DataTypes.BIGINT,
        allowNull: false
    },
    start_lat: {
        type: DataTypes.DECIMAL(10, 8),
        allowNull: false
    },
    start_lng: {
        type: DataTypes.DECIMAL(11, 8),
        allowNull: false
    },
    end_lat: {
        type: DataTypes.DECIMAL(10, 8),
        allowNull: false
    },
    end_lng: {
        type: DataTypes.DECIMAL(11, 8),
        allowNull: false
    },
    status: {
        type: DataTypes.STRING,
        defaultValue: 'active'
    },
    vehicle_id: {
        type: DataTypes.BIGINT,
        allowNull: true
    },
    updated_start_lat: {
        type: DataTypes.DECIMAL(20, 8),
        allowNull: true
    },
    updated_start_lng: {
        type: DataTypes.DECIMAL(20, 8),
        allowNull: true
    },
    updated_end_lat: {
        type: DataTypes.DECIMAL(20, 8),
        allowNull: true
    },
    updated_end_lng: {
        type: DataTypes.DECIMAL(20, 8),
        allowNull: true
    },
    polyline: {
        type: DataTypes.TEXT('long'),
        allowNull: true
    },
    polyline_encoded: {
        type: DataTypes.TEXT('long'),
        allowNull: true
    },
    distance: {
        type: DataTypes.STRING,
        allowNull: true
    },
    duration: {
        type: DataTypes.STRING,
        allowNull: true
    },
    start_address: {
        type: DataTypes.STRING,
        allowNull: true
    },
    end_address: {
        type: DataTypes.STRING,
        allowNull: true
    },
    start_city: {
        type: DataTypes.STRING,
        allowNull: true
    },
    start_state: {
        type: DataTypes.STRING,
        allowNull: true
    },
    end_city: {
        type: DataTypes.STRING,
        allowNull: true
    },
    end_state: {
        type: DataTypes.STRING,
        allowNull: true
    }
}, {
    sequelize,
    modelName: 'Trip',
    tableName: 'trips',
    timestamps: true,
    underscored: true
});

export default Trip; 